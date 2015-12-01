<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

use LogicException;

/**
 * @property AbstractQuery $query
 */
trait BuildMethod
{
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
}
