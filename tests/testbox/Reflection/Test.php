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
		$entity = new Entity();
		$entity->second = new SecondEntity();
		$this->testType($entity->second);

		$entity->second = null;
		$this->testType($entity->second);

		$entity->second = 1;
		$this->testType($entity->second);
	}


	/**
	 * @param SecondEntity|null $var
	 */
	private function testType($var) {}
}
