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
     * @var array
     */
    private $operations = [];

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
        $operations = $this->operations;
        $operations[] = [$method, $parameters];

        return $this->cloneWith("operations", $operations);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    public function cloneWith($key, $value)
    {
        $clone = clone $this;
        $clone->$key = $value;

        return $clone;
    }

    /**
     * @param string $table
     *
     * @return static
     */
    public function table($table)
    {
        return $this->cloneWith("builder", $this->builder->table($table));
    }

    /**
     * @param string $columns
     *
     * @return static
     */
    public function select($columns = "*")
    {
        return $this->cloneWith("builder", $this->builder->select($columns));
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
            $builder = $this->applyOperationsTo($this->builder);

            list($statement, $values) = $builder->build();
            yield $this->connector->query($statement, $values);
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    private function applyOperationsTo($builder)
    {
        foreach ($this->operations as $operation) {
            list($method, $parameters) = $operation;
            $builder = call_user_func_array([$builder, $method], $parameters);
        }

        return $builder;
    }

    /**
     * @param array $data
     *
     * @return PromiseInterface
     */
    public function insert(array $data)
    {
        return Coroutine\create(function () use ($data) {
            $builder = $this->builder->insert($data);
            $builder = $this->applyOperationsTo($builder);

            list($statement, $values) = $builder->build();
            yield $this->connector->query($statement, $values);
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
            $builder = $this->builder->update($data);
            $builder = $this->applyOperationsTo($builder);

            list($statement, $values) = $builder->build();
            yield $this->connector->query($statement, $values);
        });
    }

    /**
     * @return PromiseInterface
     */
    public function delete()
    {
        return Coroutine\create(function () {
            $builder = $this->builder->delete();
            $builder = $this->applyOperationsTo($builder);

            list($statement, $values) = $builder->build();
            yield $this->connector->query($statement, $values);
        });
    }
}
