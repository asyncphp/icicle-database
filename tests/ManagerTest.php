<?php

namespace AsyncPHP\Icicle\Cache\Test\Driver;

use AsyncPHP\Icicle\Database\ManagerFactory;
use PHPUnit_Framework_TestCase;
use Icicle\Loop;
use Icicle\Coroutine;

/**
 * @covers ManagerFactory
 */
class MemoryDriverTest extends PHPUnit_Framework_TestCase
{
    public function testInsert()
    {
        Coroutine\create(function() {
            $factory = new ManagerFactory();

            $manager = $factory->create([
                "driver" => "mysql",
                "username" => "root",
                "password" => "",
                "database" => "icicle"
            ]);

            $time = time();

            yield $manager->table("test1")->insert(["text" => $time]);

            $row = (yield $manager
                ->table("test1")
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
        Loop\timer($delay, function() use ($expected, $actual) {
            $this->assertEquals($expected, $actual);
            Loop\stop();
        });
    }
}
