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

$connector = $factory->create([
    "driver" => getenv("ICICLE_DRIVER"),
    "database" => getenv("ICICLE_DATABASE"),
    "username" => getenv("ICICLE_USERNAME"),
    "password" => getenv("ICICLE_PASSWORD"),
]);

$server = new Server(function (RequestInterface $request, SocketInterface $socket) use ($connector) {
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
