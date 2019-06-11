<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection\Annotations;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedPropertyReflection;
use PHPStan\Type\Type;


class AnnotationPropertyReflection extends \PHPStan\Reflection\Annotations\AnnotationPropertyReflection
	implements ExtendedPropertyReflection
{
	/** @var Type */
	private $writableType;


	public function __construct(
		ClassReflection $declaringClass,
		Type $readableType,
		Type $writableType,
		bool $readable = true,
		bool $writable = true
	)
	{
		parent::__construct($declaringClass, $readableType, $readable, $writable);
		$this->writableType = $writableType;
	}


	public function getWritableType(): Type
	{
		return $this->writableType;
	}


	public function canChangeTypeAfterAssignment(): bool
	{
		return false;
	}
}
