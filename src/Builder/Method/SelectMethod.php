<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\QueryInterface;
use LogicException;

/**
 * @property string $table
 * @property QueryInterface $query
 */
trait SelectMethod
{
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

        $query = $this->factory()->newSelect();
        $query->from($this->table);
        $query->cols($columns);

        return $this->cloneWith("query", $query);
    }

    /**
     * @return QueryFactory
     */
    abstract protected function factory();

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    abstract protected function cloneWith($key, $value);
}
