<?php

namespace AsyncPHP\Icicle\Database;

use Icicle\Coroutine;
use Icicle\Promise\PromiseInterface;

final class Manager
{
    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @param Connector $connector
     * @param Builder $builder
     */
    public function __construct(Connector $connector, Builder $builder)
    {
        $this->connector = $connector;
        $this->builder = $builder;
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, array $parameters = [])
    {
        $builder = call_user_func_array([$this->builder, $method], $parameters);

        $clone = clone $this;
        $clone->builder = $builder;

        return $clone;
    }

    /**
     * @return PromiseInterface
     */
    public function first()
    {
        return Coroutine\create(function () {
            $rows = (yield $this->limit(1)->get());

            if (count($rows) > 0) {
                yield $rows[0];
            } else {
                yield [];
            }
        });
    }

    /**
     * @return PromiseInterface
     */
    public function get()
    {
        return Coroutine\create(function () {
            list($statement, $values) = $this->builder->build();

            $query = preg_replace_callback("/\\:([_0-9a-zA-Z]+)/", function($matches) use ($values) {
                return "'" . $this->connector->escape($values[$matches[1]]) . "'";
            }, $statement);

            yield $this->connector->query($query);
        });
    }

    /**
     * @return PromiseInterface
     */
    public function run()
    {
        return $this->get();
    }
}
