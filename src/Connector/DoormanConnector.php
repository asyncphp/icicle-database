<?php

namespace AsyncPHP\Icicle\Database\Connector;

use AsyncPHP\Doorman\Manager;
use AsyncPHP\Doorman\Manager\ProcessManager;
use AsyncPHP\Doorman\Task\ProcessCallbackTask;
use AsyncPHP\Icicle\Database\Connector;
use AsyncPHP\Remit\Client;
use AsyncPHP\Remit\Client\ZeroMqClient;
use AsyncPHP\Remit\Location\InMemoryLocation;
use AsyncPHP\Remit\Server;
use AsyncPHP\Remit\Server\ZeroMqServer;
use Aura\Sql\ExtendedPdo;
use Icicle\Loop;
use Icicle\Promise\Deferred;
use Icicle\Promise\PromiseInterface;
use InvalidArgumentException;
use PDO;

final class DoormanConnector implements Connector
{
    /**
     * @var int
     */
    private $id = 1;

    /**
     * @var array
     */
    private $deferred = [];

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Client
     */
    private $client;

    /**
     * @inheritdoc
     *
     * @param array $config
     *
     * @return PromiseInterface
     *
     * @throws InvalidArgumentException
     */
    public function connect(array $config)
    {
        $this->manager = new ProcessManager();

        $this->validate($config);
        $this->connectRemit($config);
        $this->connectDoorman($config);

        $this->server->addListener("r", function ($result, $id) {
            if (isset($this->deferred[$id])) {
                $this->deferred[$id]->resolve($result);
                unset($this->deferred[$id]);
            }
        });

        $this->server->addListener("e", function ($error, $id) {
            if (isset($this->deferred[$id])) {
                $this->deferred[$id]->reject($error);
                unset($this->deferred[$id]);
            }
        });

        Loop\periodic(0, function () {
            $this->server->tick();
        });

        $this->manager->tick();
    }

    /**
     * @param array $config
     *
     * @throws InvalidArgumentException
     */
    private function validate(array $config)
    {
        if (!isset($config["remit"])) {
            throw new InvalidArgumentException("Undefined remit");
        }

        if (!isset($config["remit"]["driver"])) {
            throw new InvalidArgumentException("Undefined remit driver");
        }

        if (!isset($config["remit"]["server"])) {
            throw new InvalidArgumentException("Undefined remit server");
        }

        if (!isset($config["remit"]["client"])) {
            throw new InvalidArgumentException("Undefined remit client");
        }

        if ($config["remit"]["driver"] === "zeromq") {
            if (!isset($config["remit"]["server"]["port"])) {
                throw new InvalidArgumentException("Undefined remit server port");
            }

            if (!isset($config["remit"]["client"]["port"])) {
                throw new InvalidArgumentException("Undefined remit client port");
            }
        } else {
            throw new InvalidArgumentException("Unrecognised remit driver");
        }
    }

    /**
     * @param array $config
     */
    private function connectRemit(array $config)
    {
        $server = $config["remit"]["server"];
        $client = $config["remit"]["client"];

        if ($config["remit"]["driver"] === "zeromq") {
            $server = array_merge([
                "host" => "127.0.0.1",
            ], $server);

            $this->server = new ZeroMqServer(
                new InMemoryLocation(
                    $server["host"],
                    $server["port"]
                )
            );

            $client = array_merge([
                "host" => "127.0.0.1",
            ], $client);

            $this->client = new ZeroMqClient(
                new InMemoryLocation(
                    $client["host"],
                    $client["port"]
                )
            );
        }
    }

    /**
     * @param array $config
     */
    private function connectDoorman(array $config)
    {
        $server = $config["remit"]["server"];
        $client = $config["remit"]["client"];

        if ($config["remit"]["driver"] === "zeromq") {
            $task = new ProcessCallbackTask(function () use ($config, $server, $client) {
                $config = array_merge([
                    "host" => "127.0.0.1",
                    "port" => 3306,
                    "charset" => "utf8",
                    "socket" => null,
                ], $config);

                $server = array_merge([
                    "host" => "127.0.0.1",
                ], $server);

                $client = array_merge([
                    "host" => "127.0.0.1",
                ], $client);

                $remitServer = new ZeroMqServer(
                    new InMemoryLocation(
                        $client["host"],
                        $client["port"]
                    )
                );

                $remitClient = new ZeroMqClient(
                    new InMemoryLocation(
                        $server["host"],
                        $server["port"]
                    )
                );

                if ($config["driver"] === "mysql") {
                    $dsn = $this->mysql($config);
                }

                if ($config["driver"] === "pgsql") {
                    $dsn = $this->pgsql($config);
                }

                if ($config["driver"] === "sqlite") {
                    $dsn = $this->sqlite($config);
                    $config["username"] = null;
                    $config["password"] = null;
                }

                if ($config["driver"] === "sqlsrv") {
                    $dsn = $this->sqlsrv($config);
                }

                $connection = new ExtendedPdo(
                    new PDO($dsn, $config["username"], $config["password"])
                );

                $remitServer->addListener("q", function ($query, $values, $id) use ($remitClient, $connection) {
                    $remitClient->emit("r", [$connection->fetchAll($query, $values), $id]);
                });

                Loop\periodic(0, function () use ($remitServer) {
                    $remitServer->tick();
                });

                Loop\run();
            });

            $this->manager->addTask($task);
        }
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function mysql(array $config)
    {
        $host = (string) $config["host"];
        $port = (int) $config["port"];
        $database = (string) $config["database"];
        $socket = (string) $config["socket"];
        $charset = (string) $config["charset"];

        return "mysql:host={$host};port={$port};dbname={$database};unix_socket={$socket};charset={$charset}";
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function pgsql(array $config)
    {
        $host = (string) $config["host"];
        $port = (int) $config["port"];
        $database = (string) $config["database"];

        return "pgsql:host={$host};port={$port};dbname={$database}";
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function sqlite(array $config)
    {
        $file = (string) $config["file"];

        return "sqlite:{$file}";
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function sqlsrv(array $config)
    {
        $host = (string) $config["host"];
        $port = (int) $config["port"];
        $database = (string) $config["database"];

        return "sqlsrv:Server={$host},{$port};Database={$database}";
    }

    /**
     * @inheritdoc
     *
     * @param string $query
     * @param array $values
     *
     * @return PromiseInterface
     *
     * @throws InvalidArgumentException
     */
    public function query($query, $values)
    {
        $id = $this->id++;

        $deferred = new Deferred();

        $this->client->emit("q", [$query, $values, "d{$id}"]);

        $this->deferred["d{$id}"] = $deferred;

        return $deferred->getPromise();
    }
}
