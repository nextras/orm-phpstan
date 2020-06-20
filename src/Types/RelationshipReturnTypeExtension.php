<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\HasMany;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;


class RelationshipReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	public function getClass(): string
	{
		return HasMany::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'get' || $methodReflection->getName() === 'toCollection';
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$varType = $scope->getType($methodCall->var);

		if ($varType instanceof IntersectionType) {
			foreach ($varType->getTypes() as $type) {
				if ($type instanceof IterableType) {
					$collectionType = new ObjectType(ICollection::class);
					return TypeCombinator::intersect($type, $collectionType);
				}
			}
		}

		return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
	}
}
