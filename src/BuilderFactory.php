<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Builder\AuraBuilder;
use Aura\SqlQuery\QueryFactory;
use InvalidArgumentException;

final class BuilderFactory
{
    /**
     * @param array $config
     *
     * @return Builder
     *
     * @throws InvalidArgumentException
     */
    public function create(array $config)
    {
        $config = $this->validate($config);

        return new AuraBuilder(
            new QueryFactory($config["driver"])
        );
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

        return $config;
    }
}
