PHPStan for Nextras Orm
=======================

[![Build](https://github.com/nextras/orm-phpstan/actions/workflows/build.yml/badge.svg)](https://github.com/nextras/orm-phpstan/actions/workflows/build.yml)

PHPStan extension for Nextras Orm.

### Usage

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev nextras/orm-phpstan
```

We recommend using [PHPStan auto extension installer](https://github.com/phpstan/extension-installer), but you may include this extension manually by including `extension.neon` in your project's PHPStan config:

```
includes:
	- vendor/nextras/orm-phpstan/extension.neon
```
 
### License

MIT. See full [license](license.md).
