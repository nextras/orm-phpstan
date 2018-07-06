<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Rules;


class Test
{
	public function testOk()
	{
		$entity = new Entity();
		$entity->setValue('age', 1);
		$entity->setValue('description', 'test');
		$entity->setValue('createdAt', new \DateTimeImmutable());
		$entity->setReadOnlyValue('read', 2);
	}


	public function testError()
	{
		$entity = new Entity();
		$entity->setValue('age', '');
		$entity->setValue('description', 2);
		$entity->setValue('createdAt', new \DateTime());
		$entity->setValue('foo', '');
		$entity->setValue('read', 2);
		$entity->setReadOnlyValue('age', '');
	}
}
