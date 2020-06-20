<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\IRepository;
use Nextras\Orm\Repository\Repository;
use Nextras\OrmPhpStan\Types\Helpers\RepositoryEntityTypeHelper;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;


class RepositoryReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	/** @var RepositoryEntityTypeHelper */
	private $repositoryEntityTypeHelper;

	/** @var ReflectionProvider */
	private $reflectionProvider;


	public function __construct(RepositoryEntityTypeHelper $repositoryEntityTypeHelper, ReflectionProvider $reflectionProvider)
	{
		$this->repositoryEntityTypeHelper = $repositoryEntityTypeHelper;
		$this->reflectionProvider = $reflectionProvider;
	}


	public function getClass(): string
	{
		return IRepository::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		static $methods = [
			'getBy',
			'getByChecked',
			'getById',
			'getByIdChecked',
			'findAll',
			'findBy',
			'findById',
			'persist',
			'persistAndFlush',
			'remove',
			'removeAndFlush',
		];
		return in_array($methodReflection->getName(), $methods, true);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$repository = $scope->getType($methodCall->var);
		\assert($repository instanceof TypeWithClassName);

		if ($repository->getClassName() === Repository::class) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		/** @phpstan-var class-string<Repository> $repositoryClassName */
		$repositoryClassName = $repository->getClassName();

		try {
			$repositoryReflection = $this->reflectionProvider->getClass($repositoryClassName);
		} catch (ClassNotFoundException $e) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		$entityType = $this->repositoryEntityTypeHelper->resolveFirst(
			$repositoryReflection,
			$scope
		);

		static $collectionReturnMethods = [
			'findAll',
			'findBy',
			'findById',
		];

		static $entityReturnMethods = [
			'getBy',
			'getById',
		];

		static $entityNonNullReturnMethods = [
			'getByChecked',
			'getByIdChecked',
			'persist',
			'persistAndFlush',
			'remove',
			'removeAndFlush',
		];

		$methodName = $methodReflection->getName();
		if (in_array($methodName, $collectionReturnMethods, true)) {
			return new IntersectionType([
				new ObjectType(ICollection::class),
				new IterableType(new IntegerType(), $entityType),
			]);
		} elseif (in_array($methodName, $entityNonNullReturnMethods, true)) {
			return $entityType;
		} elseif (in_array($methodName, $entityReturnMethods, true)) {
			return TypeCombinator::addNull($entityType);
		}

		throw new ShouldNotHappenException();
	}
}
