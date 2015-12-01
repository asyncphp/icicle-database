<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\ConnectorFactory;
use Icicle\Http\Message\RequestInterface;
use Icicle\Http\Message\Response;
use Icicle\Http\Server\Server;
use Icicle\Loop;
use Icicle\Socket\SocketInterface;
use Icicle\Stream\MemorySink;

$factory = new ConnectorFactory();

$connector = $factory->create(require(__DIR__ . "/config.php"));

$server = new Server(function (RequestInterface $request, SocketInterface $socket) use ($connector) {
    try {
        yield $connector->query(
            "delete from test1"
        );

        for ($i = 0; $i < 5; $i++) {
            yield $connector->query(
                "insert into test1 (text) values ('foo')"
            );
        }

        $result = (yield $connector->query(
            "select * from test1"
        ));
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
