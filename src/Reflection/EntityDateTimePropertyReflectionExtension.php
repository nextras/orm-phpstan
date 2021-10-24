<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection;

use Nextras\Orm\Entity\IEntity;
use Nextras\OrmPhpStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;


class EntityDateTimePropertyReflectionExtension implements PropertiesClassReflectionExtension
{
	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		$property = $classReflection->getPropertyTags()[$propertyName] ?? null;
		if ($property === null) {
			return false;
		}

		$interfaces = array_map(function (ClassReflection $interface) {
			return $interface->getName();
		}, $classReflection->getInterfaces());
		if (!in_array(IEntity::class, $interfaces, true)) {
			return false;
		}

		$propertyType = TypeCombinator::removeNull($property->getType()); // remove null to properly match subtype
		$dateTimeType = new ObjectType(\DateTimeImmutable::class);
		$hasDateTime = $dateTimeType->isSuperTypeOf($propertyType)->yes();

		return $hasDateTime;
	}


	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		$property = $classReflection->getPropertyTags()[$propertyName] ?? null;
		if ($property === null) {
			throw new ShouldNotHappenException();
		}

		return new AnnotationPropertyReflection(
			$classReflection,
			$property->getType(),
			TypeCombinator::union($property->getType(), new StringType()),
			$property->isReadable(),
			$property->isWritable()
		);
	}
}
