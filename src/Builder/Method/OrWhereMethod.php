<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use Aura\SqlQuery\Common\WhereInterface;
use LogicException;

/**
 * @property string $table
 * @property WhereInterface $query
 */
trait OrWhereMethod
{
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
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    abstract protected function cloneWith($key, $value);
}
