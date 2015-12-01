<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\Common\SelectInterface;
use LogicException;

/**
 * @property string $table
 * @property SelectInterface $query
 */
trait OrderByMethod
{
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
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    abstract protected function cloneWith($key, $value);
}
