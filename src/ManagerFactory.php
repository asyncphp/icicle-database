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
     * @throws InvalidArgumentException
     */
    public function create(array $config)
    {
        $connectors = new ConnectorFactory();
        $builders = new BuilderFactory();

        return new Manager(
            $connectors->create($config),
            $builders->create($config)
        );
    }
}
