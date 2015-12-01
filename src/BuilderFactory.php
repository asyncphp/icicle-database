<?php

namespace AsyncPHP\Icicle\Database;

use AsyncPHP\Icicle\Database\Builder\MySQLBuilder;
use InvalidArgumentException;

final class BuilderFactory
{
    /**
     * @param array $config
     *
     * @return Builder
     *
     * @throw InvalidArgumentException
     */
    public function create(array $config)
    {
        if (!isset($config["driver"])) {
            throw new InvalidArgumentException("Undefined driver");
        }

        if ($config["driver"] === "mysql") {
            return new MySQLBuilder();
        }

        throw new InvalidArgumentException("Unrecognised driver");
    }
}
