<?php

require __DIR__ . "/../vendor/autoload.php";

use AsyncPHP\Icicle\Database\Connection\MySQLConnection;
use Icicle\Http\Message\RequestInterface;
use Icicle\Http\Message\Response;
use Icicle\Http\Server\Server;
use Icicle\Loop;
use Icicle\Socket\SocketInterface;
use Icicle\Stream\MemorySink;

$connection = new MySQLConnection();

$connection->connect([
    "database" => "icicle",
    "username" => "",
    "password" => "",
]);

$server = new Server(function (RequestInterface $request, SocketInterface $socket) use ($connection) {
    yield $connection->query(
        "delete from test1"
    );

    for ($i = 0; $i < 5; $i++) {
        yield $connection->query(
            "insert into test1 (text) values ('foo')"
        );
    }

    $result = (yield $connection->query(
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
