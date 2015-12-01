<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\Common\LimitOffsetInterface;
use LogicException;

/**
 * @property string $table
 * @property LimitOffsetInterface $query
 */
trait LimitMethod
{
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
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    abstract protected function cloneWith($key, $value);
}
