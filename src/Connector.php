<?php

namespace AsyncPHP\Icicle\Database;

use Icicle\Promise\PromiseInterface;
use InvalidArgumentException;

interface Connector
{
    /**
     * Connects to a database.
     *
     * @param array $config
     *
     * @return PromiseInterface
     *
     * @throws InvalidArgumentException
     */
    public function connect(array $config);

    /**
     * Prepares and executes a statement from a query, and resolves to an array of results.
     * Manipulation queries resolve without an value.
     *
     * @param string $query
     * @param array $values
     *
     * @return PromiseInterface
     *
     * @throws InvalidArgumentException
     */
    public function query($query, $values);

    /**
     * Disconnects from a database.
     */
    public function disconnect();
}
