<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Rules;

use Nextras\Orm\Entity\IEntity;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;


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

		if (!is_string($node->name) || !in_array($node->name, ['setValue', 'setReadOnlyValue'], true)) {
			return [];
		}
		$args = $node->args;
		if (!isset($args[0], $args[1])) {
			return [];
		}
		$valueType = $scope->getType($args[1]->value);
		$varType = $scope->getType($node->var);
		if ($varType->getClass() === null) {
			return [];
		}
		$firstValue = $args[0]->value;
		if (!$firstValue instanceof Node\Scalar\String_) {
			return [];
		}
		$fieldName = $firstValue->value;
		$class = $this->broker->getClass($varType->getClass());
		$interfaces = array_map(function (ClassReflection $interface) {
			return $interface->getName();
		}, $class->getInterfaces());
		if (!in_array(IEntity::class, $interfaces, true)) {
			return [];
		}
		if (!$class->hasProperty($fieldName)) {
			return [sprintf('Entity %s has no property named %s', $varType->getClass(), $fieldName)];
		}
		$property = $class->getProperty($fieldName);
		$propertyType = $property->getType();
		if (!$propertyType->accepts($valueType)) {
			return [sprintf('Entity %s: property $%s (%s) does not accept %s', $varType->getClass(), $fieldName, $propertyType->describe(), $valueType->describe())];
		}
		if ($node->name === 'setReadOnlyValue' && (!$scope->isInClass() || !$scope->hasVariableType('this') || !$varType->accepts($scope->getVariableType('this')))) {
			return [sprintf('You cannot set readonly property $%s on entity %s', $fieldName, $varType->getClass())];
		}
		return [];
	}
}
