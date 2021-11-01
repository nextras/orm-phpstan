<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Reflection\Annotations;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;


class AnnotationPropertyReflection implements PropertyReflection
{
	/** @var Type */
	private $writableType;

	/** @var ClassReflection */
	private $declaringClass;

	/** @var Type */
	private $readableType;

	/** @var bool */
	private $readable;

	/** @var bool */
	private $writable;


	public function __construct(
		ClassReflection $declaringClass,
		Type $readableType,
		Type $writableType,
		bool $readable = true,
		bool $writable = true
	)
	{
		$this->writableType = $writableType;
		$this->declaringClass = $declaringClass;
		$this->readableType = $readableType;
		$this->readable = $readable;
		$this->writable = $writable;
	}


	public function getWritableType(): Type
	{
		return $this->writableType;
	}


	public function canChangeTypeAfterAssignment(): bool
	{
		return false;
	}


	public function getDeclaringClass(): \PHPStan\Reflection\ClassReflection
	{
		return $this->declaringClass;
	}


	public function isStatic(): bool
	{
		return false;
	}


	public function isPrivate(): bool
	{
		return false;
	}


	public function isPublic(): bool
	{
		return true;
	}


	public function getDocComment(): ?string
	{
		return null;
	}


	public function getReadableType(): \PHPStan\Type\Type
	{
		return $this->readableType;
	}


	public function isReadable(): bool
	{
		return $this->readable;
	}


	public function isWritable(): bool
	{
		return $this->writable;
	}


	public function isDeprecated(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}


	public function getDeprecatedDescription(): ?string
	{
		return null;
	}


	public function isInternal(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}
}
