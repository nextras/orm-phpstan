<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Type;

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


	public function getClass()
	{
		return $this->originalType->getClass();
	}


	public function getReferencedClasses(): array
	{
		return $this->originalType->getReferencedClasses();
	}


	public function combineWith(Type $otherType): Type
	{
		return $this->originalType->combineWith($otherType);
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


	public function isIterable(): int
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
}
