<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Model\IModel;
use Nextras\Orm\Repository\IRepository;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantTypeHelper;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class ModelGetRepositoryForEntityReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	/**
	 * @var Broker
	 */
	private $broker;

	/**
	 * @var array<class-string<IEntity>, array<int, ConstantStringType>>
	 */
	private $repositoriesCache;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getClass(): string
	{
		return IModel::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getRepositoryForEntity';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type {
		if ($this->repositoriesCache === null) {
			$this->repositoriesCache = $this->getRepositories();
		}

		$entityClassNameType = $scope->getType($methodCall->args[0]->value);

		if (!$entityClassNameType instanceof ConstantStringType) {
			throw new \Exception($entityClassNameType);
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		$entityClassName = $entityClassNameType->getValue();
		if (!array_key_exists($entityClassName, $this->repositoriesCache)) {
			throw new \Exception(print_r($this->repositoriesCache, true));
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		return TypeCombinator::union(...$this->repositoriesCache[$entityClassName]);
	}

	private function getRepositories(): array
	{
		$map = [];

		foreach (get_declared_classes() as $class) {
			if (!(new \ReflectionClass($class))->implementsInterface(IRepository::class)) {
				continue;
			}

			foreach ($class::getEntityClassNames() as $entityClassName) {
				$map[$entityClassName][] = ConstantTypeHelper::getTypeFromValue($class);
			}
		}

		return $map;
	}
}
