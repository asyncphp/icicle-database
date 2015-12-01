<?php

namespace AsyncPHP\Icicle\Database;

use InvalidArgumentException;

final class ManagerFactory
{
    /**
     * @param array $config
     *
     * @return Manager
     *
     * @throw InvalidArgumentException
     */
    public function create(array $config)
    {
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined driver");
        }

        if (!in_array($config["driver"], ["sqlsrv", "mysql", "pgsql", "sqlite"])) {
            throw new InvalidArgumentException("Unrecognised driver");
        }

        $connectors = new ConnectorFactory();
        $builders = new BuilderFactory();

        return new Manager(
            $connectors->create($config),
            $builders->create($config)
        );
    }
}
