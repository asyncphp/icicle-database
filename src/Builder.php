<?php

namespace AsyncPHP\Icicle\Database;

use LogicException;

interface Builder
{
    /**
     * @param string $table
     *
     * @return static
     */
    public function table($table);

    /**
     * @param string $columns
     *
     * @return static
     *
     * @throws LogicException
     */
    public function select($columns = "*");

    /**
     * @param mixed $where
     *
     * @return static
     *
     * @throws LogicException
     */
    public function where($where);

    /**
     * @param mixed $where
     *
     * @return static
     *
     * @throws LogicException
     */
    public function orWhere($where);

    /**
     * @param string $order
     *
     * @return static
     *
     * @throws LogicException
     */
    public function orderBy($order);

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return static
     *
     * @throws LogicException
     */
    public function limit($limit, $offset = 0);

    /**
     * @param array $data
     *
     * @return static
     *
     * @throws LogicException
     */
    public function insert(array $data);

    /**
     * @param array $data
     *
     * @return static
     *
     * @throws LogicException
     */
    public function update(array $data);

    /**
     * @return static
     *
     * @throws LogicException
     */
    public function delete();

    /**
     * @return array
     *
     * @throws LogicException
     */
    public function build();
}
