<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Entity\IEntity;


class RepositoryTypesTest
{
	public function testError(AuthorsRepository $repository, BooksRepository $booksRepository)
	{
		$this->takeAuthor($repository->getById(1));
		$this->takeAuthor($repository->getBy([]));

		/** @var IEntity $a */
		$a = $repository->getById(1);
		$this->takeAuthor($booksRepository->persist($a));
	}


	public function testOk(AuthorsRepository $repository)
	{
		$this->takeAuthorNullable($repository->getById(1));
		$this->takeAuthorNullable($repository->getBy([]));
		$this->takeAuthorNullable($repository->findAll()->fetch());
		$this->takeAuthorNullable($repository->findBy([])->getById(1));
		$this->takeAuthorNullable($repository->findBy([])->orderBy([])->limitBy(2, 0)->getById(1));
		$this->takeAuthorArray($repository->findAll()->fetchAll());

		/** @var IEntity $a */
		$a = $repository->getById(1);
		$this->takeAuthor($repository->persist($a));
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
