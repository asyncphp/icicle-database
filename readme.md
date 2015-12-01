# Icicle Cache

[![Build Status](http://img.shields.io/travis/asyncphp/icicle-database.svg?style=flat-square)](https://travis-ci.org/asyncphp/icicle-database)
[![Code Quality](http://img.shields.io/scrutinizer/g/asyncphp/icicle-database.svg?style=flat-square)](https://scrutinizer-ci.com/g/asyncphp/icicle-database)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/asyncphp/icicle-database.svg?style=flat-square)](https://scrutinizer-ci.com/g/asyncphp/icicle-database)
[![Version](http://img.shields.io/packagist/v/asyncphp/icicle-database.svg?style=flat-square)](https://packagist.org/packages/asyncphp/icicle-database)
[![License](http://img.shields.io/packagist/l/asyncphp/icicle-database.svg?style=flat-square)](license.md)

A simple database library, built for Icicle, with promises.

## Usage

```php
$factory = new ConnectionFactory();

$connection = $factory->create([
    "driver" => getenv("ICICLE_DRIVER"),
    "database" => getenv("ICICLE_DATABASE"),
    "username" => getenv("ICICLE_USERNAME"),
    "password" => getenv("ICICLE_PASSWORD"),
]);

yield $connection->query(
    "select * from pages"
);
```

## Caveats

- `mysql` is the only supported driver
- `mysql` driver does not support prepared statements
- `join`, `groupBy`, `having`, `orHaving`, `distinct` methods are missing

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.
