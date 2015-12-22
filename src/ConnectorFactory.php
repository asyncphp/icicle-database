<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Connector\BlockingConnector;
use AsyncPHP\Icicle\Database\Connector\DoormanConnector;
use InvalidArgumentException;

final class ConnectorFactory
{
    /**
     * @param array $config
     *
     * @return Connector
     *
     * @throws InvalidArgumentException
     */
    public function create(array $config)
    {
        $config = $this->validate($config);

        if ($config["connector"] === "doorman") {
            $connector = new DoormanConnector();
        }

        if ($config["connector"] === "blocking") {
            $connector = new BlockingConnector();
        }

        $connector->connect($config);

        return $connector;
    }

    /**
     * @param array $config
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function validate(array $config)
    {
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined driver");
        }

        if (!in_array($config["driver"], ["sqlsrv", "mysql", "pgsql", "sqlite"])) {
            throw new InvalidArgumentException("Unrecognised driver");
        }

        if (!isset($config["connector"])) {
            throw new InvalidArgumentException("Undefined connector");
        }

        if (!in_array($config["connector"], ["doorman", "blocking"])) {
            throw new InvalidArgumentException("Unrecognised connector");
        }

        return $config;
    }
}
