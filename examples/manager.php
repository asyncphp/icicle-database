<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\Builder\MySQLBuilder;
use AsyncPHP\Icicle\Database\Connector\MySQLConnector;
use AsyncPHP\Icicle\Database\Manager;
use Icicle\Coroutine;
use Icicle\Loop;

Coroutine\create(function () {
    $config = [
        "driver" => getenv("ICICLE_DRIVER"),
        "database" => getenv("ICICLE_DATABASE"),
        "username" => getenv("ICICLE_USERNAME"),
        "password" => getenv("ICICLE_PASSWORD"),
    ];

    $manager = new Manager(
        $connector = new MySQLConnector(),
        new MySQLBuilder($config)
    );

    $connector->connect($config);

    print_r(yield $manager->table("test1")->select()->get());
});

Loop\run();
