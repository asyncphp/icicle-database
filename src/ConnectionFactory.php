<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Connection\MySQLConnection;
use InvalidArgumentException;

final class ConnectionFactory
{
    /**
     * @param array $config
     *
     * @return Connection
     *
     * @throw InvalidArgumentException
     */
    public function create(array $config)
    {
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined connection driver");
        }

        if ($config["driver"] === "mysql") {
            $connection = new MySQLConnection();
            $connection->connect($config);

            return $connection;
        }

        throw new InvalidArgumentException("Unrecognised connection driver");
    }
}
