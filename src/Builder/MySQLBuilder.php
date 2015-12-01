<?php

namespace AsyncPHP\Icicle\Database\Builder;

use AsyncPHP\Icicle\Database\Builder;
use AsyncPHP\Icicle\Database\Builder\Method\BuildMethod;
use AsyncPHP\Icicle\Database\Builder\Method\CloneWithMethod;
use AsyncPHP\Icicle\Database\Builder\Method\DeleteMethod;
use AsyncPHP\Icicle\Database\Builder\Method\InsertMethod;
use AsyncPHP\Icicle\Database\Builder\Method\LimitMethod;
use AsyncPHP\Icicle\Database\Builder\Method\OrderByMethod;
use AsyncPHP\Icicle\Database\Builder\Method\OrWhereMethod;
use AsyncPHP\Icicle\Database\Builder\Method\SelectMethod;
use AsyncPHP\Icicle\Database\Builder\Method\TableMethod;
use AsyncPHP\Icicle\Database\Builder\Method\UpdateMethod;
use AsyncPHP\Icicle\Database\Builder\Method\WhereMethod;
use Aura\SqlQuery\QueryFactory;

final class MySQLBuilder implements Builder
{
    use BuildMethod;
    use CloneWithMethod;
    use DeleteMethod;
    use InsertMethod;
    use LimitMethod;
    use OrderByMethod;
    use OrWhereMethod;
    use SelectMethod;
    use TableMethod;
    use UpdateMethod;
    use WhereMethod;

    /**
     * @var QueryFactory
     */
    protected $factory;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @return QueryFactory
     */
    protected function factory()
    {
        if (!isset($this->factory)) {
            $this->factory = new QueryFactory("mysql");
        }

        return $this->factory;
    }
}
