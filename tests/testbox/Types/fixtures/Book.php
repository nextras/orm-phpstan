<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

/**
 * @property Author $author {m:1 Author::$books}
 * @property \DateTimeImmutable|null $date
 */
class Book extends \Nextras\Orm\Entity\Entity
{
}
