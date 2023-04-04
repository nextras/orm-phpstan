<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Repository\Repository;


/**
 * @extends Repository<Author>
 */
class AuthorsRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [Author::class];
	}
}
