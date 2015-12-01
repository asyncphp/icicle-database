<?php

namespace AsyncPHP\Icicle\Database;

use Icicle\Promise\PromiseInterface;

interface Connector
{
    /**
     * @param array $config
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function connect(array $config);

    /**
     * Runs a query and resolves to an array of results for query statements. Manipulation
     * statements resolve without an value.
     *
     * @param string $query
     *
     * @return PromiseInterface
     */
    public function query($query);

    /**
     * Escapes a value for interpolation.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function escape($value);
}
