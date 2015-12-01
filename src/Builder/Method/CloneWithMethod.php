<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

trait CloneWithMethod
{
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
}
