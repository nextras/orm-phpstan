<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection;

use Nextras\Orm\Entity\IEntity;
use Nextras\OrmPhpStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\IntegerType;
use PHPStan\Type\NeverType;
use PHPStan\Type\TypeCombinator;


class EntityRelationshipPropertyReflectionExtension implements PropertiesClassReflectionExtension
{
	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		$hasProperty = array_key_exists($propertyName, $classReflection->getPropertyTags());
		if (!$hasProperty) {
			return false;
		}

		$interfaces = array_map(function (ClassReflection $interface) {
			return $interface->getName();
		}, $classReflection->getInterfaces());
		if (!in_array(IEntity::class, $interfaces, true)) {
			return false;
		}

		$phpDoc = $classReflection->getNativeReflection()->getDocComment();
		if (!$phpDoc) {
			return false;
		}

		$regexp = '#\$' . preg_quote($propertyName, '#') . '[^\n]+\{[1m]:1.+}.*$#m';
		$hasRelationship = preg_match($regexp, $phpDoc) === 1;
		return $hasRelationship;
	}


	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		$property = $classReflection->getPropertyTags()[$propertyName] ?? null;
		if ($property === null || $property->getReadableType() === null) {
			throw new ShouldNotHappenException();
		}

		return new AnnotationPropertyReflection(
			$classReflection,
			$property->getReadableType(),
			TypeCombinator::union($property->getWritableType() ?? new NeverType(), new IntegerType()),
			$property->isReadable(),
			$property->isWritable()
		);
	}
}
