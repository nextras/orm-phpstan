<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Rules;

use DateTimeImmutable;


/**
 * @property int $age
 * @property string $description
 * @property DateTimeImmutable $createdAt
 * @property-read int $read
 */
class Entity extends \Nextras\Orm\Entity\Entity
{
}
