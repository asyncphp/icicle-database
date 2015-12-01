<?php

namespace AsyncPHP\Icicle\Database;

use Icicle\Promise\PromiseInterface;

interface Connection
{
    /**
     * @param array $config
     *
     * @return bool
     */
    public function connect(array $config);

    /**
     * Runs a query and resolves to an array of results for query statements. Manipulation statements
     * resolve without an value.
     *
     * @param string $query
     *
     * @return PromiseInterface
     */
    public function query($query);
}
