<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\ManagerFactory;
use Icicle\Http\Message\RequestInterface;
use Icicle\Http\Message\Response;
use Icicle\Http\Server\Server;
use Icicle\Loop;
use Icicle\Socket\SocketInterface;
use Icicle\Stream\MemorySink;

$config = [
    "driver" => getenv("ICICLE_DRIVER"),
    "database" => getenv("ICICLE_DATABASE"),
    "username" => getenv("ICICLE_USERNAME"),
    "password" => getenv("ICICLE_PASSWORD"),
];

$factory = new ManagerFactory();
$manager = $factory->create($config);

$server = new Server(function (RequestInterface $request, SocketInterface $socket) use ($manager) {
    yield $manager->table("test1")->delete()->run();

    for ($i = 0; $i < 5; $i++) {
        yield $manager->table("test1")->insert(["text" => "foo"])->run();
    }

    $result = (yield $manager->table("test1")->select()->get());

    $sink = new MemorySink();
    yield $sink->end(count($result));

    $response = new Response(200, [
        "Content-type" => "text/plain",
        "Content-length" => $sink->getLength(),
    ], $sink);

    yield $response;
});

$server->listen(8000);

Loop\run();
