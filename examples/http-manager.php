<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\ManagerFactory;
use Icicle\Http\Message\RequestInterface;
use Icicle\Http\Message\Response;
use Icicle\Http\Server\Server;
use Icicle\Loop;
use Icicle\Socket\SocketInterface;
use Icicle\Stream\MemorySink;

$factory = new ManagerFactory();
$manager = $factory->create(require(__DIR__ . "/config.php"));

$server = new Server(function (RequestInterface $request, SocketInterface $socket) use ($manager) {
    try {
        yield $manager->table("test")->delete();

        for ($i = 0; $i < 5; $i++) {
            yield $manager->table("test")->insert(["text" => "foo"]);
        }

        $result = (yield $manager->table("test")->select()->get());
    } catch (Exception $e) {
        die($e->getMessage());
    }

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
