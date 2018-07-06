<?php declare(strict_types = 1);

namespace Nextras\OrmPhpStan\Types;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Nextras\Orm\Repository\Repository;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Parser\Parser;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;


class RepositoryReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	/** @var Parser */
	private $parser;


	public function __construct(Parser $parser)
	{
		$this->parser = $parser;
	}


	public function getClass(): string
	{
		return IRepository::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		static $methods = [
			'getBy',
			'getById',
			'findAll',
			'findBy',
			'findById',
		];
		return in_array($methodReflection->getName(), $methods, true);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$repository = $scope->getType($methodCall->var);
		\assert($repository instanceof TypeWithClassName);

		if ($repository->getClassName() === Repository::class) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		$repositoryReflection = new \ReflectionClass($repository->getClassName());
		$entityClassNameTypes = $this->parseEntityClassNameTypes(
			$repositoryReflection->getFileName(),
			$repository->getClassName(),
			$scope
		);

		if ($entityClassNameTypes === null) {
			$entityType = new ObjectType(IEntity::class);
		} else {
			assert($entityClassNameTypes instanceof ConstantArrayType);
			$classNameType = $entityClassNameTypes->getFirstValueType();
			assert($classNameType instanceof ConstantStringType);
			$entityType = new ObjectType($classNameType->getValue());
		}

		static $collectionReturnMethods = [
			'findAll',
			'findBy',
			'findById',
		];

		static $entityReturnMethods = [
			'getBy',
			'getById',
		];

		$methodName = $methodReflection->getName();
		if (in_array($methodName, $collectionReturnMethods, true)) {
			return new IntersectionType([
				new ObjectType(ICollection::class),
				new IterableType(new IntegerType(), $entityType),
			]);
		} elseif (in_array($methodName, $entityReturnMethods, true)) {
			return TypeCombinator::addNull($entityType);
		}

		throw new ShouldNotHappenException();
	}


	private function parseEntityClassNameTypes(string $fileName, string $className, Scope $scope): ?Type
	{
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

		if ($return instanceof Node\Stmt\Return_) {
			return $scope->getType($return->expr);
		} else {
			return null;
		}
	}
}
