<?php

namespace AsyncPHP\Icicle\Database\Connector;

use AsyncPHP\Icicle\Database\Connector;
use Aura\Sql\ExtendedPdo;
use Icicle\Promise;
use Icicle\Promise\PromiseInterface;
use InvalidArgumentException;
use PDO;

final class BlockingConnector implements Connector
{
    /**
     * @var ExtendedPdo
     */
    private $connection;

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
        $config = $this->validate($config);

        $this->connection = new ExtendedPdo(
            new PDO($this->newConnectionString($config), $config["username"], $config["password"])
        );
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
            return sprintf("mysql:host=%s;port=%s;dbname=%s;unix_socket=%s;charset=%s", $config["host"], $config["port"], $config["schema"], $config["socket"], $config["charset"]);
        }

        if ($config["driver"] === "pgsql") {
            return sprintf("pgsql:host=%s;port=%s;dbname=%s", $config["host"], $config["port"], $config["schema"]);
        }

        if ($config["driver"] === "sqlite") {
            return sprintf("sqlite:%s", $config["file"]);
        }

        if ($config["driver"] === "sqlsrv") {
            return sprintf("sqlsrv:Server=%s,%s;Database=%s", $config["host"], $config["port"], $config["schema"]);
        }
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
        $config += [
            "host" => "127.0.0.1",
            "port" => 3306,
            "charset" => "utf8",
            "socket" => null,
        ];

        // TODO: validate connection details

        return $config;
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
        return Promise\resolve(
            $this->connection->fetchAll($query, $values)
        );
    }
}
