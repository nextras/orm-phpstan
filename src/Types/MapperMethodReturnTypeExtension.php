<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\Dbal\DbalMapper;
use Nextras\OrmPhpStan\Types\Helpers\RepositoryEntityTypeHelper;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;


class MapperMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	/** @var RepositoryEntityTypeHelper */
	private $repositoryEntityTypeHelper;


	public function __construct(RepositoryEntityTypeHelper $repositoryEntityTypeHelper)
	{
		$this->repositoryEntityTypeHelper = $repositoryEntityTypeHelper;
	}


	public function getClass(): string
	{
		return DbalMapper::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		static $methods = [
			'toEntity',
			'toCollection',
		];
		return in_array($methodReflection->getName(), $methods, true);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$mapper = $scope->getType($methodCall->var);
		\assert($mapper instanceof TypeWithClassName);

		$defaultReturn = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

		if ($mapper->getClassName() === DbalMapper::class) {
			return $defaultReturn;
		}

		$mapperClass = $mapper->getClassName();
		do {
			/** @phpstan-var class-string<\Nextras\Orm\Repository\Repository> $repositoryClass */
			$repositoryClass = \str_replace('Mapper', 'Repository', $mapperClass);
			$mapperClass = \get_parent_class($mapperClass);
			assert(is_string($mapperClass));
		} while (!\class_exists($repositoryClass) && $mapperClass !== DbalMapper::class);

		try {
			$repository = new \ReflectionClass($repositoryClass);
		} catch (\ReflectionException $e) {
			return $defaultReturn;
		}

		$entityType = $this->repositoryEntityTypeHelper->resolveFirst(
			$repository,
			$scope
		);

		$methodName = $methodReflection->getName();
		if ($methodName === 'toEntity') {
			return TypeCombinator::addNull($entityType);
		} elseif ($methodName === 'toCollection') {
			return TypeCombinator::intersect(
				new ObjectType(ICollection::class),
				new IterableType(new IntegerType(), $entityType)
			);
		}

		throw new ShouldNotHappenException();
	}
}
