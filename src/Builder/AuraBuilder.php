<?php

namespace AsyncPHP\Icicle\Database\Builder;

use AsyncPHP\Icicle\Database\Builder;
use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\QueryInterface;
use LogicException;

final class AuraBuilder implements Builder
{
    /**
     * @var QueryFactory
     */
    protected $factory;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param QueryFactory $factory
     */
    public function __construct(QueryFactory $factory)
    {
        $this->factory = $factory;
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
     * @return array
     *
     * @throws LogicException
     */
    public function build()
    {
        if (!isset($this->query)) {
            throw new LogicException("build() called before select(), insert(), update() or delete()");
        }

        return [
            $this->query->getStatement(),
            $this->query->getBindValues(),
        ];
    }

    /**
     * @return static
     *
     * @throws LogicException
     */
    public function delete()
    {
        if (!isset($this->table)) {
            throw new LogicException("delete() called before table()");
        }

        $query = $this->factory->newDelete();
        $query->from($this->table);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param array $data
     *
     * @return static
     *
     * @throws LogicException
     */
    public function insert(array $data)
    {
        if (!isset($this->table)) {
            throw new LogicException("insert() called before table()");
        }

        $query = $this->factory->newInsert();
        $query->into($this->table);
        $query->cols($data);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return static
     *
     * @throws LogicException
     */
    public function limit($limit, $offset = 0)
    {
        if (!isset($this->query)) {
            throw new LogicException("limit() called before select()");
        }

        $query = clone $this->query;
        $query->limit($limit);
        $query->offset($offset);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param string $order
     *
     * @return static
     *
     * @throws LogicException
     */
    public function orderBy($order)
    {
        if (!isset($this->query)) {
            throw new LogicException("orderBy() called before select()");
        }

        if (!is_array($order)) {
            $order = [$order];
        }

        $query = clone $this->query;
        $query->orderBy($order);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param mixed $where
     *
     * @return static
     *
     * @throws LogicException
     */
    public function orWhere($where)
    {
        if (!isset($this->query)) {
            throw new LogicException("orWhere() called before select(), update() or delete()");
        }

        $query = clone $this->query;
        call_user_func_array([$query, "orWhere"], func_get_args());

        return $this->cloneWith("query", $query);
    }

    /**
     * @param string $columns
     *
     * @return static
     *
     * @throws LogicException
     */
    public function select($columns = "*")
    {
        if (!isset($this->table)) {
            throw new LogicException("select() called before table()");
        }

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $query = $this->factory->newSelect();
        $query->from($this->table);
        $query->cols($columns);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param string $table
     *
     * @return static
     */
    public function table($table)
    {
        return $this->cloneWith("table", $table);
    }

    /**
     * @param array $data
     *
     * @return static
     *
     * @throws LogicException
     */
    public function update(array $data)
    {
        if (!isset($this->table)) {
            throw new LogicException("update() called before table()");
        }

        $query = $this->factory->newUpdate();
        $query->table($this->table);
        $query->cols($data);

        return $this->cloneWith("query", $query);
    }

    /**
     * @param mixed $where
     *
     * @return static
     *
     * @throws LogicException
     */
    public function where($where)
    {
        if (!isset($this->query)) {
            throw new LogicException("where() called before select(), update() or delete()");
        }

        $query = clone $this->query;
        call_user_func_array([$query, "where"], func_get_args());

        return $this->cloneWith("query", $query);
    }
}
