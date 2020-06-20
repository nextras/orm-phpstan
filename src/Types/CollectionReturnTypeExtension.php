<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;


class CollectionReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	public function getClass(): string
	{
		return ICollection::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		static $methods = [
			'getBy',
			'getByChecked',
			'getById',
			'getByIdChecked',
			'findBy',
			'orderBy',
			'resetOrderBy',
			'limitBy',
			'fetch',
			'fetchAll',
		];
		return in_array($methodReflection->getName(), $methods, true);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		static $collectionReturnMethods = [
			'findBy',
			'orderBy',
			'resetOrderBy',
			'limitBy',
		];

		static $entityReturnMethods = [
			'getBy',
			'getById',
			'fetch',
		];

		static $entityNonNullReturnMethods = [
			'getByChecked',
			'getByIdChecked',
		];

		$varType = $scope->getType($methodCall->var);
		$methodName = $methodReflection->getName();

		if (!$varType instanceof IntersectionType) {
			if (in_array($methodName, $collectionReturnMethods, true)) {
				return $varType;
			} else {
				return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
			}
		}

		if (in_array($methodName, $collectionReturnMethods, true)) {
			return $varType;

		} elseif (in_array($methodName, $entityReturnMethods, true)) {
			return TypeCombinator::addNull($varType->getIterableValueType());

		} elseif (in_array($methodName, $entityNonNullReturnMethods, true)) {
			return $varType->getIterableValueType();

		} elseif ($methodName === 'fetchAll') {
			return new ArrayType(new IntegerType(), $varType->getIterableValueType());
		}

		throw new ShouldNotHappenException();
	}
}
