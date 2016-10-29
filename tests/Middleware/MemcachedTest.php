<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\Soranoba\Pcache\Middleware;

use Soranoba\Pcache\Cache;
use Soranoba\Pcache\Middleware\Memcached;
use Tests\Soranoba\Pcache\MiddlewareTest;

class MemcacheTest extends MiddlewareTest
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::instance(Memcached::class, [
            Memcached::SERVERS_KEY => [
                ["127.0.0.1", 11212, 100]
            ]
        ]);
    }

    public function testInstanceName()
    {
        $this->assertEquals(
            "127.0.0.1:11212+127.0.0.1:11213+localhost:11213",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => [
                    ["127.0.0.1", 11213],
                    ["127.0.0.1", 11212],
                    ["localhost", 11213],
                ]
            ])
        );
        $this->assertEquals(
            "127.0.0.1:11212+127.0.0.1:11213+localhost:11213",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => [
                    ["localhost", 11213],
                    ["127.0.0.1", 11212],
                    ["127.0.0.1", 11213],
                ]
            ])
        );
    }

    public function testInstanceNameEmptyServers()
    {
        $this->assertEquals(
            "",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => [
                ]
            ])
        );
    }

    public function testInstanceNameInvalidOptions()
    {
        $this->assertEquals(
            "",
            Memcached::instanceName([
            ])
        );
        $this->assertEquals(
            "",
            Memcached::instanceName([
                Memcached::SERVERS_KEY => "dummy"
            ])
        );
    }
}
