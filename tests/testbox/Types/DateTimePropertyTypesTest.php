<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

class DateTimePropertyTypesTest
{
	public function testError(Book $book): void
	{
		$book->date = 1;
	}


	public function testOk(Book $book): void
	{
		$book->date = 'now';
		$this->takeNullableDateTime($book->date);
		$book->date = null;
	}


	private function takeNullableDateTime(?\DateTimeImmutable $data): void
	{
	}
}
