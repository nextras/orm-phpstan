<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

class RepositoryTypesTest
{
	public function testError(AuthorsRepository $repository)
	{
		$this->takeAuthor($repository->getById(1));
		$this->takeAuthor($repository->getBy([]));
	}


	public function testOk(AuthorsRepository $repository)
	{
		$this->takeAuthorNullable($repository->getById(1));
		$this->takeAuthorNullable($repository->getBy([]));
		$this->takeAuthorNullable($repository->findAll()->fetch());
		$this->takeAuthorNullable($repository->findBy([])->getById(1));
		$this->takeAuthorNullable($repository->findBy([])->orderBy([])->limitBy(2, 0)->getById(1));
		$this->takeAuthorArray($repository->findAll()->fetchAll());
	}


	private function takeAuthor(Author $author)
	{
	}


	private function takeAuthorNullable(?Author $author)
	{
	}


	/**
	 * @param array<int, Author> $authors
	 */
	private function takeAuthorArray($authors)
	{
	}
}
