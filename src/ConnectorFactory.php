<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Connector\DoormanConnector;
use AsyncPHP\Icicle\Database\Connector\MySQLConnector;
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
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined driver");
        }

        if (!in_array($config["driver"], ["sqlsrv", "mysql", "pgsql", "sqlite"])) {
            throw new InvalidArgumentException("Unrecognised driver");
        }

        $connector = new DoormanConnector();
        $connector->connect($config);

        return $connector;
    }
}
