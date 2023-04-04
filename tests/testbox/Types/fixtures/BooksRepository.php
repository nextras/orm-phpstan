<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Repository\Repository;


/**
 * @extends Repository<Book>
 */
class BooksRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [Book::class];
	}
}
