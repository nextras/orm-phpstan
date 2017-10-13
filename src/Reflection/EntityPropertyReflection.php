<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection;

use Nextras\OrmPhpStan\Type\HasOneRelationshipType;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;


class EntityPropertyReflection implements PropertyReflection
{
	/** @var PropertyReflection */
	private $propertyReflection;


	public function __construct(PropertyReflection $propertyReflection)
	{
		$this->propertyReflection = $propertyReflection;
	}


	public function getDeclaringClass(): ClassReflection
	{
		return $this->propertyReflection->getDeclaringClass();
	}


	public function isStatic(): bool
	{
		return $this->propertyReflection->isStatic();
	}


	public function isPrivate(): bool
	{
		return $this->propertyReflection->isPrivate();
	}


	public function isPublic(): bool
	{
		return $this->propertyReflection->isPublic();
	}


	public function getType(): Type
	{
		return new HasOneRelationshipType($this->propertyReflection->getType());
	}


	public function isReadable(): bool
	{
		return $this->propertyReflection->isReadable();
	}


	public function isWritable(): bool
	{
		return $this->propertyReflection->isWritable();
	}
}
