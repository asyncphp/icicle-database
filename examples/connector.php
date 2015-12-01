<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\Connector\DoormanConnector;
use Icicle\Coroutine;
use Icicle\Loop;

Coroutine\create(function () {
    $connector = new DoormanConnector();

    $connector->connect(require(__DIR__ . "/config.php"));

    try {
        yield $connector->query("insert into test (text) values (:text)", ["text" => "foo"]);
    } catch (Exception $e) {
        print $e->getMessage();
    }

    while (true) {
        yield $connector->query("select * from test where text = :text", ["text" => "foo"]);
        print ".";
    }
});

Loop\run();
