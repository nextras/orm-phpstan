<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Type;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassConstantReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;


class HasOneRelationshipType implements Type
{
	/** @var Type */
	private $originalType;


	public function __construct(Type $type)
	{
		$this->originalType = $type;
	}


	public function accepts(Type $type): bool
	{
		if ($type instanceof IntegerType) {
			return true;
		} else {
			return $this->originalType->accepts($type);
		}
	}


	public function getReferencedClasses(): array
	{
		return $this->originalType->getReferencedClasses();
	}


	public function combineWith(Type $otherType): Type
	{
		return $this->originalType;
	}


	public function describe(): string
	{
		return $this->originalType->describe();
	}


	public function canAccessProperties(): bool
	{
		return $this->originalType->canAccessProperties();
	}


	public function canCallMethods(): bool
	{
		return $this->originalType->canCallMethods();
	}


	public function isDocumentableNatively(): bool
	{
		return $this->originalType->isDocumentableNatively();
	}


	public function isIterable(): TrinaryLogic
	{
		return $this->originalType->isIterable();
	}


	public function getIterableKeyType(): Type
	{
		return $this->originalType->getIterableKeyType();
	}


	public function getIterableValueType(): Type
	{
		return $this->originalType->getIterableValueType();
	}


	public static function __set_state(array $properties): Type
	{
		return new self($properties['originalType']);
	}


	public function isSupersetOf(Type $type): TrinaryLogic
	{
		// trial to fix something
		if ($type instanceof IntegerType) {
			return TrinaryLogic::createNo();
		} else {
			return $this->originalType->isSupersetOf($type);
		}
	}


	public function hasProperty(string $propertyName): bool
	{
		return $this->originalType->hasProperty($propertyName);
	}


	public function getProperty(string $propertyName, Scope $scope): PropertyReflection
	{
		return $this->originalType->getProperty($propertyName, $scope);
	}


	public function hasMethod(string $methodName): bool
	{
		return $this->originalType->hasMethod($methodName);
	}


	public function getMethod(string $methodName, Scope $scope): MethodReflection
	{
		return $this->originalType->getMethod($methodName, $scope);
	}


	public function canAccessConstants(): bool
	{
		return $this->originalType->canAccessConstants();
	}


	public function hasConstant(string $constantName): bool
	{
		return $this->originalType->hasConstant($constantName);
	}


	public function getConstant(string $constantName): ClassConstantReflection
	{
		return $this->originalType->getConstant($constantName);
	}


	public function isCallable(): TrinaryLogic
	{
		return $this->originalType->isCallable();
	}


	public function isClonable(): bool
	{
		return $this->originalType->isClonable();
	}
}
