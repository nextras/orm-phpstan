<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Reflection;


class Test
{
	public function testError()
	{
		$entity = new Entity();
		$entity->second = 'string';
		$entity->secondFake = 1;
	}


	public function testOk()
	{
		$test = function (?SecondEntity $var) {};

		$entity = new Entity();
		$entity->second = new SecondEntity();
		$test($entity->second);

		$entity->second = null;
		$test($entity->second);

		$entity->second = 1;
		$test($entity->second);
	}
}
