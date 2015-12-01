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

        $connectors = new ConnectorFactory();
        $builders = new BuilderFactory();

        if ($config["driver"] === "mysql") {
            return new Manager(
                $connectors->create($config),
                $builders->create($config)
            );
        }

        throw new InvalidArgumentException("Unrecognised driver");
    }
}
