<?php

namespace AsyncPHP\Icicle\Database\Builder\Method;

/**
 * @property string $table
 */
trait TableMethod
{
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
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    abstract protected function cloneWith($key, $value);
}
