<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\QueryInterface;
use LogicException;

/**
 * @property string $table
 * @property QueryInterface $query
 */
trait DeleteMethod
{
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

        $query = $this->factory()->newDelete();
        $query->from($this->table);

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
