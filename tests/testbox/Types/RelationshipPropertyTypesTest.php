<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;


class RelationshipPropertyTypesTest
{
	public function testError(Book $book): void
	{
		$book->author = 1;
		$this->takeInt($book->author);
		$book->author = new Author();
		$this->takeInt($book->author);
		$book->author = new Book();
	}


	public function testOk(Book $book): void
	{
		$book->author = 1;
		$this->takeAuthor($book->author);
		$book->author = new Author();
		$this->takeAuthor($book->author);
	}


	private function takeAuthor(Author $author): void
	{
	}


	private function takeInt(int $int): void
	{
	}
}
