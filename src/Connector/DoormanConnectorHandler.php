<?php

namespace AsyncPHP\Icicle\Database\Connector;

use AsyncPHP\Doorman\Handler;
use AsyncPHP\Doorman\Task;
use AsyncPHP\Remit\Client;
use AsyncPHP\Remit\Client\ZeroMqClient;
use AsyncPHP\Remit\Location\InMemoryLocation;
use AsyncPHP\Remit\Server;
use AsyncPHP\Remit\Server\ZeroMqServer;
use Aura\Sql\ExtendedPdo;
use Icicle\Loop;
use PDO;

final class DoormanConnectorHandler implements Handler
{
    /**
     * @inheritdoc
     *
     * @param Task $task
     */
    public function handle(Task $task)
    {
        $config = $task->getData();

        if ($config["driver"] === "mysql") {
            $config += [
                "host" => "127.0.0.1",
                "port" => 3306,
                "charset" => "utf8",
                "socket" => null,
            ];
        }

        if ($config["remit"]["driver"] === "zeromq") {
            $config["remit"]["server"] += [
                "host" => "127.0.0.1",
            ];

            $config["remit"]["client"] += [
                "host" => "127.0.0.1",
            ];

            $server = new ZeroMqServer(
                new InMemoryLocation(
                    $config["remit"]["client"]["host"],
                    $config["remit"]["client"]["port"]
                )
            );

            $client = new ZeroMqClient(
                new InMemoryLocation(
                    $config["remit"]["server"]["host"],
                    $config["remit"]["server"]["port"]
                )
            );
        }

        $connection = new ExtendedPdo(
            new PDO($this->newConnectionString($config), $config["username"], $config["password"])
        );

        $server->addListener("q", function ($query, $values, $id) use ($client, $connection) {
            $client->emit("r", [$connection->fetchAll($query, $values), $id]);
        });

        $server->addListener("d", function () use ($connection, $server, $client) {
            $client->emit("dd");

            try {
                $connection->disconnect();
            } catch (Exception $exception) {
                // TODO: find an elegant way to deal with this
            }

            $server->disconnect();
            $client->disconnect();

            Loop\stop();
        });

        Loop\periodic(0, function () use ($server) {
            $server->tick();
        });

        Loop\run();
    }

    /**
     * Returns a new dsn string, for PDO to connect to the database with.
     *
     * @param array $config
     *
     * @return string
     */
    private function newConnectionString(array $config)
    {
        if ($config["driver"] === "mysql") {
            return "mysql:host={$config['host']};port={$config['port']};dbname={$config['schema']};unix_socket={$config['socket']};charset={$config['charset']}";
        }

        if ($config["driver"] === "pgsql") {
            return "pgsql:host={$config['host']};port={$config['port']};dbname={$config['schema']}";
        }

        if ($config["driver"] === "sqlite") {
            return "sqlite:{$config['file']}";
        }

        if ($config["driver"] === "sqlsrv") {
            return "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['schema']}";
        }
    }
}
