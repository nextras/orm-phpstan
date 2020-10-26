PHPStan for Nextras Orm
=======================

[![Build Status](https://github.com/nextras/orm-phpstan/workflows/Build/badge.svg?branch=master)](https://github.com/nextras/orm-phpstan/actions?query=workflow%3ABuild+branch%3Amaster)

PHPStan extension for Nextras Orm.

### Usage

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev nextras/orm-phpstan
```

We recommend using [PHPStan auto extension installer](phpstan/extension-installer), but you may include this extension manually by including `extension.neon` in your project's PHPStan config:

```
includes:
	- vendor/nextras/orm-phpstan/extension.neon
```
 
### License

MIT. See full [license](license.md).
