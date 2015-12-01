<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\ManagerFactory;
use Icicle\Coroutine;
use Icicle\Loop;

Coroutine\create(function () {
    $factory = new ManagerFactory();
    $manager = $factory->create(require(__DIR__ . "/config.php"));

    try {
        yield $manager->table("test")->select()->where("text = ?", "foo")->limit(1, 2)->orderBy("text desc")->get();
        yield $manager->table("test")->insert(["text" => "bar"]);
        yield $manager->table("test")->where("text = ?", "bar")->update(["text" => "foo"]);
        yield $manager->table("test")->where("text = ?", "bar")->delete();
    } catch (Exception $e) {
        die($e->getMessage());
    }
});

Loop\run();
