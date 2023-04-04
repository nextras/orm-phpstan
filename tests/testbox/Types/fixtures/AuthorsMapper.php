<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\Dbal\DbalMapper;


/**
 * @extends DbalMapper<Author>
 */
class AuthorsMapper extends DbalMapper
{
	/** @return ICollection<Author> */
	public function findAllWithTranslatedIps(): ICollection
	{
		return $this->toCollection(
			$this->builder()->addSelect('inet6_ntoa([ip]) as [ip]')
		);
	}
}
