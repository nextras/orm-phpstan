<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan\Reflection;


/**
 * @property SecondEntity|null $second {m:1 SecondEntity::$something}
 * @property SecondEntity|null $secondFake
 */
class Entity extends \Nextras\Orm\Entity\Entity
{
}


class SecondEntity extends \Nextras\Orm\Entity\Entity
{
}
