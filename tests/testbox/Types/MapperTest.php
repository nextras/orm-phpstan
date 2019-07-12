<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;


class MapperTest extends AuthorsMapper
{
	public function testOk()
	{
		$this->takeAuthors($this->toCollection([]));
		$this->takeAuthor($this->toEntity([]));
	}


	private function takeAuthor(?Author $author)
	{
	}


	/**
	 * @param iterable<int, Author> $authors
	 */
	private function takeAuthors($authors)
	{
	}
}
