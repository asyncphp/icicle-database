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
            yield reset($rows);
        });
    }

    /**
     * @return PromiseInterface
     */
    public function get()
    {
        return Coroutine\create(function () {
            yield $this->connector->query(
                $this->interpolate($this->builder->build())
            );
        });
    }

    /**
     * @param array $build
     *
     * @return string
     */
    private function interpolate(array $build)
    {
        list($statement, $values) = $build;

        return preg_replace_callback("/\\:([_0-9a-zA-Z]+)/", function ($matches) use ($values) {
            return "'" . $this->connector->escape($values[$matches[1]]) . "'";
        }, $statement);
    }

    /**
     * @param array $data
     *
     * @return PromiseInterface
     */
    public function insert(array $data)
    {
        return Coroutine\create(function () use ($data) {
            yield $this->connector->query(
                $this->interpolate($this->builder->insert($data)->build())
            );
        });
    }

    /**
     * @param array $data
     *
     * @return PromiseInterface
     */
    public function update(array $data)
    {
        return Coroutine\create(function () use ($data) {
            yield $this->connector->query(
                $this->interpolate($this->builder->update($data)->build())
            );
        });
    }

    /**
     * @return PromiseInterface
     */
    public function delete()
    {
        return Coroutine\create(function () {
            yield $this->connector->query(
                $this->interpolate($this->builder->delete()->build())
            );
        });
    }
}
