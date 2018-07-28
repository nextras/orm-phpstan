<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * @property Book[]|OneHasMany $books {1:m Book::$author}
 */
class Author extends \Nextras\Orm\Entity\Entity
{
}
