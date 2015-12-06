<?php

namespace AsyncPHP\Icicle\Database\Connector;

use AsyncPHP\Doorman\Manager;
use AsyncPHP\Doorman\Manager\ProcessManager;
use AsyncPHP\Icicle\Database\Connector;
use AsyncPHP\Remit\Client;
use AsyncPHP\Remit\Client\ZeroMqClient;
use AsyncPHP\Remit\Location\InMemoryLocation;
use AsyncPHP\Remit\Server;
use AsyncPHP\Remit\Server\ZeroMqServer;
use Icicle\Loop;
use Icicle\Promise\Deferred;
use Icicle\Promise\PromiseInterface;
use InvalidArgumentException;

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
        $this->validate($config);
        $this->remit($config);

        $this->manager = new ProcessManager();

        if (isset($config["log"])) {
            $this->manager->setLogPath($config["log"]);
        }

        $this->manager->addTask(new DoormanConnectorTask($config));
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
    private function remit(array $config)
    {
        if ($config["remit"]["driver"] === "zeromq") {
            $config["remit"]["server"] += [
                "host" => "127.0.0.1",
            ];

            $config["remit"]["client"] += [
                "host" => "127.0.0.1",
            ];

            $this->server = new ZeroMqServer(
                new InMemoryLocation(
                    $config["remit"]["server"]["host"],
                    $config["remit"]["server"]["port"]
                )
            );

            $this->client = new ZeroMqClient(
                new InMemoryLocation(
                    $config["remit"]["client"]["host"],
                    $config["remit"]["client"]["port"]
                )
            );
        }

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

        $this->server->addListener("dd", function() {
            $this->server->disconnect();
            $this->client->disconnect();
        });

        Loop\periodic(0, function () {
            $this->server->tick();
        });
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
    public function query($query, $values = [])
    {
        $id = $this->id++;

        $this->deferred["d{$id}"] = new Deferred();

        $this->client->emit("q", [$query, $values, "d{$id}"]);

        return $this->deferred["d{$id}"]->getPromise();
    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        $this->client->emit("d");
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
