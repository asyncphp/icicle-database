<?php

namespace AsyncPHP\Icicle\Cache\Test\Driver;

use AsyncPHP\Icicle\Database\ManagerFactory;
use Icicle\Coroutine;
use Icicle\Loop;
use PHPUnit_Framework_TestCase;

class ManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInsert()
    {
        Coroutine\create(function () {
            $factory = new ManagerFactory();

            $manager = $factory->create([
                "driver" => "mysql",
                "username" => "root",
                "password" => "",
                "schema" => "icicle",
                "remit" => [
                    "driver" => "zeromq",
                    "server" => [
                        "port" => 5555,
                    ],
                    "client" => [
                        "port" => 5556,
                    ],
                ],
            ]);

            $time = time();

            yield
            $manager
                ->table("test")
                ->insert(["text" => $time]);

            $row = (yield
            $manager
                ->table("test")
                ->select()
                ->where("text = ?", $time)
                ->first()
            );

            $this->assertEqualsAfterDelay(0.5, $row["text"], $time);
        })->done();

        Loop\run();
    }

    /**
     * @param float $delay
     * @param mixed $expected
     * @param mixed $actual
     */
    private function assertEqualsAfterDelay($delay, $expected, $actual)
    {
        Loop\timer($delay, function () use ($expected, $actual) {
            $this->assertEquals($expected, $actual);
            Loop\stop();
        });
    }
}
