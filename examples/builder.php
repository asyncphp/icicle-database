<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\BuilderFactory;

$factory = new BuilderFactory();

$builder = $factory->create([
    "driver" => getenv("ICICLE_DRIVER"),
]);

print_r(
    $builder
        ->table("test1")
        ->select()
        ->limit(10, 5)
        ->orderBy("id")
        ->where("foo = ?", "bar")
        ->build()
);

print_r(
    $builder
        ->table("test1")
        ->insert(["text" => "foo"])
        ->build()
);

print_r(
    $builder
        ->table("test1")
        ->update(["text" => "foo"])
        ->where("foo = ? and bar = ?", "bar", "baz")
        ->build()
);

print_r(
    $builder
        ->table("test1")
        ->delete()
        ->where("foo = ?", "bar")
        ->build()
);
