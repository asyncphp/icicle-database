<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\ManagerFactory;
use Icicle\Coroutine;
use Icicle\Loop;

Coroutine\create(function () {
    $config = [
        "driver" => getenv("ICICLE_DRIVER"),
        "database" => getenv("ICICLE_DATABASE"),
        "username" => getenv("ICICLE_USERNAME"),
        "password" => getenv("ICICLE_PASSWORD"),
    ];

    $factory = new ManagerFactory();
    $manager = $factory->create($config);

    print_r(yield $manager->table("test1")->select()->get());
});

Loop\run();
