<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Rules;

use Nextras\Orm\Entity\IEntity;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\VerbosityLevel;


class SetValueMethodRule implements Rule
{
	/** @var Broker */
	private $broker;


	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}


	public function getNodeType(): string
	{
		return MethodCall::class;
	}


	/**
	 * @param MethodCall $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$methodName = (string) $node->name;
		if (!in_array($methodName, ['setValue', 'setReadOnlyValue'], true)) {
			return [];
		}
		$args = $node->args;
		if (!isset($args[0], $args[1])) {
			return [];
		}
		$valueType = $scope->getType($args[1]->value);
		$varType = $scope->getType($node->var);
		if (!$varType instanceof TypeWithClassName) {
			return [];
		}
		$firstValue = $args[0]->value;
		if (!$firstValue instanceof Node\Scalar\String_) {
			return [];
		}
		$fieldName = $firstValue->value;
		$class = $this->broker->getClass($varType->getClassName());
		$interfaces = array_map(function (ClassReflection $interface) {
			return $interface->getName();
		}, $class->getInterfaces());
		if (!in_array(IEntity::class, $interfaces, true)) {
			return [];
		}
		if (!$class->hasProperty($fieldName)) {
			return [sprintf(
				'Entity %s has no $%s property.',
				$varType->getClassName(),
				$fieldName
			)];
		}
		$property = $class->getProperty($fieldName, $scope);
		$propertyType = $property->getType();
		if (!$propertyType->accepts($valueType, true)->yes()) {
			return [sprintf(
				'Entity %s: property $%s (%s) does not accept %s.',
				$varType->getClassName(),
				$fieldName,
				$propertyType->describe(VerbosityLevel::typeOnly()),
				$valueType->describe(VerbosityLevel::typeOnly())
			)];
		}
		if (!$property->isWritable() && $methodName !== 'setReadOnlyValue') {
			return [sprintf(
				'Entity %s: property $%s is read-only.',
				$varType->getClassName(),
				$fieldName
			)];
		}
		return [];
	}
}
