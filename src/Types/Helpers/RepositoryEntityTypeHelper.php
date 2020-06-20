<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types\Helpers;

use Nextras\Orm\Entity\IEntity;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Parser\Parser;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;


class RepositoryEntityTypeHelper
{
	/** @var Parser */
	private $parser;


	public function __construct(Parser $parser)
	{
		$this->parser = $parser;
	}

	public function resolveFirst(ClassReflection $repositoryReflection, Scope $scope): Type
	{
		$entityClassNameTypes = $this->parseEntityClassNameTypes($repositoryReflection, $scope);

		if ($entityClassNameTypes === null) {
			return new ObjectType(IEntity::class);
		} else {
			\assert($entityClassNameTypes instanceof ConstantArrayType);
			$classNameTypes = $entityClassNameTypes->getValueTypes();
			\assert(\count($classNameTypes) > 0);
			$classNameType = $classNameTypes[0];
			\assert($classNameType instanceof ConstantStringType);
			return new ObjectType($classNameType->getValue());
		}
	}

	private function parseEntityClassNameTypes(ClassReflection $repositoryReflection, Scope $scope): ?Type
	{
		$className = $repositoryReflection->getName();
		$fileName = $repositoryReflection->getFileName();

		assert($fileName !== false, sprintf('File for clsas "%s" does not exists.', $className));

		$ast = $this->parser->parseFile($fileName);

		$nodeTraverser = new NodeTraverser();
		$nodeTraverser->addVisitor(new NameResolver());
		$ast = $nodeTraverser->traverse($ast);

		$nodeFinder = new NodeFinder();
		$class = $nodeFinder->findFirst($ast, function (Node $node) use ($className) {
			return $node instanceof Node\Stmt\Class_
				&& $node->namespacedName->toString() === $className;
		});

		if ($class === null) {
			return null;
		}

		$method = $nodeFinder->findFirst($class, function (Node $node) {
			return $node instanceof Node\Stmt\ClassMethod
				&& $node->name->name === 'getEntityClassNames';
		});

		if ($method === null) {
			return null;
		}

		$return = $nodeFinder->findFirst($method, function (Node $node) {
			return $node instanceof Node\Stmt\Return_;
		});

		if ($return instanceof Node\Stmt\Return_ && $return->expr !== null) {
			return $scope->getType($return->expr);
		} else {
			return null;
		}
	}
}
