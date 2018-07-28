<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;


class RelationshipTypesTest
{
	public function testError(Author $author)
	{
		$this->takeBook($author->books->get()->fetch());
	}


	public function testOk(Author $author)
	{
		$this->takeBookNullable($author->books->get()->fetch());
		$this->takeBookNullable($author->books->get()->findBy([])->fetch());
	}


	private function takeBook(Book $book)
	{
	}


	private function takeBookNullable(?Book $book)
	{
	}
}
