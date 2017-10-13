<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan;

use Tester\Assert;
use Tester\Environment;


require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();

$command = __DIR__ . '/../vendor/bin/phpstan analyze --no-progress -l 7 -c ' . __DIR__ . '/config.neon --errorFormat rawSimple ' . __DIR__ . '/testbox';
exec($command, $o);
$actual = trim(implode("\n", $o));
$expected = trim(file_get_contents(__DIR__ . '/expected.txt'));
Assert::same($expected, $actual);
