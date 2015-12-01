<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Connector\MySQLConnector;
use InvalidArgumentException;

final class ConnectorFactory
{
    /**
     * @param array $config
     *
     * @return Connector
     *
     * @throw InvalidArgumentException
     */
    public function create(array $config)
    {
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined driver");
        }

        if ($config["driver"] === "mysql") {
            $connector = new MySQLConnector();
            $connector->connect($config);

            return $connector;
        }

        throw new InvalidArgumentException("Unrecognised driver");
    }
}
