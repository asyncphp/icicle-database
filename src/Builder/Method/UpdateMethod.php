<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\QueryInterface;
use LogicException;

/**
 * @property string $table
 * @property QueryInterface $query
 */
trait UpdateMethod
{
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

        $query = $this->factory()->newUpdate();
        $query->table($this->table);
        $query->cols($data);

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
