# Icicle Cache

[![Build Status](http://img.shields.io/travis/asyncphp/icicle-database.svg?style=flat-square)](https://travis-ci.org/asyncphp/icicle-database)
[![Code Quality](http://img.shields.io/scrutinizer/g/asyncphp/icicle-database.svg?style=flat-square)](https://scrutinizer-ci.com/g/asyncphp/icicle-database)
[![Version](http://img.shields.io/packagist/v/asyncphp/icicle-database.svg?style=flat-square)](https://packagist.org/packages/asyncphp/icicle-database)
[![License](http://img.shields.io/packagist/l/asyncphp/icicle-database.svg?style=flat-square)](license.md)

A simple database library, built for Icicle, with promises.

## Usage

```php
$connection = new AsyncPHP\Icicle\Database\MySQLConnection();

$connection->connect([
    "database" => "icicle",
    "username" => "user",
    "password" => "****",
]);

yield $connection->query(
    "select * from pages"
);
```

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.
