<?php

namespace AsyncPHP\Icicle\Database\Connection;

use AsyncPHP\Icicle\Database\Connection;
use Icicle\Loop;
use Icicle\Promise\Deferred;
use Icicle\Promise\PromiseInterface;

class MySQLConnection implements Connection
{
    /**
     * @var null|MySQLi
     */
    private $connection = null;

    /**
     * @var bool
     */
    private $ready = true;

    /**
     * @var float
     */
    private $poll = 0.000001;

    /**
     * @inheritdoc
     *
     * @param array $config
     *
     * @return bool
     */
    public function connect(array $config)
    {
        $config = array_merge([
            "host" => "127.0.0.1",
            "port" => 3306,
            "socket" => null,
        ], $config);

        $this->connection = mysqli_connect(
            $config["host"],
            $config["username"],
            $config["password"],
            $config["database"],
            $config["port"],
            $config["socket"]
        );
    }

    /**
     * @inheritdoc
     *
     * @param string $query
     *
     * @return PromiseInterface
     */
    public function query($query)
    {
        $deferred = new Deferred();

        $links = $errors = $rejects = [
            $this->connection,
        ];

        $outer = Loop\periodic($this->poll, function() use (&$outer, $links, $errors, $rejects, $query, $deferred) {
            if ($this->ready) {
                $this->ready = false;
                $outer->stop();

                $result = $this->connection->query($query, MYSQLI_ASYNC);

                if ($result === false) {
                    $this->ready = true;
                    return $deferred->reject($this->connection->error);
                }

                $inner = Loop\periodic($this->poll, function () use (&$inner, $links, $errors, $rejects, $deferred) {
                    if (mysqli_poll($links, $errors, $rejects, $this->poll)) {
                        $inner->stop();

                        $result = $this->connection->reap_async_query();

                        if ($result === false) {
                            $this->ready = true;
                            return $deferred->reject($this->connection->error);
                        }

                        if ($result === true) {
                            $this->ready = true;
                            return $deferred->resolve();
                        }

                        $rows = $result->fetch_all();
                        $result->free();

                        $this->ready = true;
                        return $deferred->resolve($rows);
                    }
                });
            }
        });

        return $deferred->getPromise();
    }
}
