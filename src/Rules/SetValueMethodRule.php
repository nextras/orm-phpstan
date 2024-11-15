<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Rules;

use Nextras\Orm\Entity\IEntity;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;


/**
 * @phpstan-implements Rule<MethodCall>
 */
class SetValueMethodRule implements Rule
{
	/** @var ReflectionProvider */
	private $reflectionProvider;


	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
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
		if (!$node->name instanceof Node\Identifier) {
			return [];
		}

		$methodName = $node->name->name;
		if (!in_array($methodName, ['setValue', 'setReadOnlyValue'], true)) {
			return [];
		}

		$args = $node->args;
		if (!isset($args[0], $args[1])) {
			return [];
		}
		if (!$args[0] instanceof Node\Arg || !$args[1] instanceof Node\Arg) {
			return [];
		}

		$valueType = $scope->getType($args[1]->value);
		$varType = $scope->getType($node->var);
		$classNames = $varType->getObjectClassNames();
		if (count($classNames) < 1) {
			return [];
		}

		$firstValue = $args[0]->value;
		if (!$firstValue instanceof Node\Scalar\String_) {
			return [];
		}
		$fieldName = $firstValue->value;

		$errors = [];
		foreach ($classNames as $className) {
			$class = $this->reflectionProvider->getClass($className);
			$interfaces = array_map(function (ClassReflection $interface) {
				return $interface->getName();
			}, $class->getInterfaces());
			if (!in_array(IEntity::class, $interfaces, true)) {
				continue;
			}

			if (!$class->hasProperty($fieldName)) {
				$errors[] = RuleErrorBuilder::message(sprintf('Entity %s has no $%s property.', $className, $fieldName))
					->identifier("nextrasOrm.propertyNotFound")
					->build();
				continue;
			}

			$property = $class->getProperty($fieldName, $scope);

			if (!$property->isWritable() && $methodName !== 'setReadOnlyValue') {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Entity %s: property $%s is read-only.',
					$className,
					$fieldName
				))
					->identifier("nextrasOrm.propertyReadOnly")
					->build();
				continue;
			}

			$propertyType = $property->getWritableType();
			if (!$property->isWritable()) {
				$propertyType = $property->getReadableType();
			}

			if (!$propertyType->accepts($valueType, true)->yes()) {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Entity %s: property $%s (%s) does not accept %s.',
					$className,
					$fieldName,
					$propertyType->describe(VerbosityLevel::typeOnly()),
					$valueType->describe(VerbosityLevel::typeOnly())
				))
					->identifier("nextrasOrm.propertyUnresolvableType")
					->build();
			}
		}

		return $errors;
	}
}
