<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Types;

use Nextras\Orm\Model\Model;
use Nextras\Orm\Repository\IRepository;

class ModelTypesTest
{
	public function testGetRepository(Model $em): AuthorsRepository
	{
		return $em->getRepository(AuthorsRepository::class);
	}

	public function testGetUnknownRepository(Model $em, string $repository): IRepository
	{
		return $em->getRepository($repository);
	}

	public function testGetRepositoryForEntity(Model $em): AuthorsRepository
	{
		return $em->getRepositoryForEntity(Author::class);
	}

	public function testGetEntityFromRepositoryForEntity(Model $em): ?Author
	{
		return $em->getRepositoryForEntity(Author::class)->getById(9);
	}
}
