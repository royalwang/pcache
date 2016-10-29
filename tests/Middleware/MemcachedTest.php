<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\pcache\Middleware;

use pcache\Cache;
use pcache\Middleware\Memcached;
use Tests\pcache\MiddlewareTest;

class MemcacheTest extends MiddlewareTest
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::instance(Memcached::class, [
            Memcached::SERVERS_KEY => [
                ["127.0.0.1", 11211, 100]
            ]
        ]);
    }

    public function testInstanceName()
    {
        $this->assertEquals(
            "127.0.0.1:11211:100+127.0.0.1:11212:50+127.0.0.1:11212:100+localhost:11212:20",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => [
                    ["127.0.0.1", 11212, 100],
                    ["127.0.0.1", 11211, 100],
                    ["localhost", 11212, 20],
                    ["127.0.0.1", 11212, 50],
                ]
            ])
        );
        $this->assertEquals(
            "127.0.0.1:11211:100+127.0.0.1:11212:50+127.0.0.1:11212:100+localhost:11212:20",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => [
                    ["localhost", 11212, 20],
                    ["127.0.0.1", 11211, 100],
                    ["127.0.0.1", 11212, 50],
                    ["127.0.0.1", 11212, 100],
                ]
            ])
        );
    }
}
