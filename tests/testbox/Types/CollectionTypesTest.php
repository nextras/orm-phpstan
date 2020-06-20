<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;


class CollectionTypesTest
{
	/**
	 * @param Author[]|ICollection $collection
	 */
	public function testError($collection): void
	{
		$this->takeAuthor($collection->getById(1));
		$this->takeAuthor($collection->getBy([]));
	}


	/**
	 * @param Author[]|ICollection $collection
	 */
	public function testOk($collection): void
	{
		$this->takeAuthor($collection->getByIdChecked(1));
		$this->takeAuthor($collection->getByChecked(['id' => 1]));
		$this->takeAuthorNullable($collection->getById(1));
		$this->takeAuthorNullable($collection->getBy([]));
		$this->takeAuthorNullable($collection->findBy([])->getById(1));
		$this->takeAuthorNullable($collection->findBy([])->orderBy([])->limitBy(2, 0)->getById(1));
		$this->takeAuthor($collection->findBy([])->getByIdChecked(1));
		$this->takeAuthor($collection->findBy([])->orderBy([])->limitBy(2, 0)->getByIdChecked(1));
		$this->takeAuthorArray($collection->fetchAll());
	}


	private function takeAuthor(Author $author): void
	{
	}


	private function takeAuthorNullable(?Author $author): void
	{
	}


	/**
	 * @param array<int, Author> $authors
	 */
	private function takeAuthorArray($authors): void
	{
	}
}
