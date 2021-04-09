<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection;

use Nextras\Orm\Entity\IEntity;
use Nextras\OrmPhpStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\Annotations\AnnotationsPropertiesClassReflectionExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;


class EntityDateTimePropertyReflectionExtension implements PropertiesClassReflectionExtension
{
	/** @var AnnotationsPropertiesClassReflectionExtension */
	private $annotationsExtension;


	public function __construct(AnnotationsPropertiesClassReflectionExtension $annotationsExtension)
	{
		$this->annotationsExtension = $annotationsExtension;
	}


	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		$hasProperty = $this->annotationsExtension->hasProperty($classReflection, $propertyName);
		if (!$hasProperty) {
			return false;
		}

		$interfaces = array_map(function (ClassReflection $interface) {
			return $interface->getName();
		}, $classReflection->getInterfaces());
		if (!in_array(IEntity::class, $interfaces, true)) {
			return false;
		}

		$property = $this->annotationsExtension->getProperty($classReflection, $propertyName);
		$propertyType = TypeCombinator::removeNull($property->getReadableType()); // remove null to be properly match subtype
		$dateTimeType = new ObjectType(\DateTimeImmutable::class);
		$hasDateTime = $dateTimeType->isSuperTypeOf($propertyType)->yes();

		return $hasDateTime;
	}


	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		$property = $this->annotationsExtension->getProperty($classReflection, $propertyName);
		return new AnnotationPropertyReflection(
			$property->getDeclaringClass(),
			$property->getReadableType(),
			TypeCombinator::union($property->getWritableType(), new StringType()),
			$property->isReadable(),
			$property->isWritable()
		);
	}
}
