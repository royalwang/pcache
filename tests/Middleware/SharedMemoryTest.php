<?php

/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\pcache\Middleware;

use pcache\Cache;
use pcache\Middleware\SharedMemory;
use Tests\pcache\MiddlewareTest;

class SharedMemoryTest extends MiddlewareTest
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::instance("SharedMemory", [SharedMemory::OPEN_KEY => 1024]);
    }

    public function testAutoExtension()
    {
        $instance = new SharedMemory([
            SharedMemory::OPEN_KEY => mt_rand(1, 65535),
            SharedMemory::SIZE_KEY => 1,
            SharedMemory::AUTO_DELETE => true
        ]);
        $instance->set("a", "aaaaa");
        $this->assertEquals("aaaaa", $instance->get("a"));
        $instance->set("b", "bbbbb");
        $instance->set("c", "ccccc");
        $this->assertEquals(["a" => "aaaaa", "b" => "bbbbb", "c" => "ccccc"], $instance->getAll());
    }
}
