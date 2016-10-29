<?php

/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\pcache;

use pcache\Cache;
use pcache\TTL;

abstract class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $cache = null;

    public function setUp()
    {
        if (!$this->cache instanceof Cache) {
            $this->fail("Cache MUST be initialize on construct");
        }
        $this->cache->deleteAll();
    }

    public function tearDown()
    {
        $this->cache->deleteAll();
    }

    public function testGetAndSet()
    {
        $key = __METHOD__;
        $value = "test_value";
        $this->assertEquals(false, $this->cache->get($key));
        $this->assertEquals(true, $this->cache->set($key, $value));
        $this->assertEquals($value, $this->cache->get($key));
    }

    public function testExpire()
    {
        $key = __METHOD__;
        $value = "test_value";
        $this->assertEquals(true, $this->cache->set($key, $value, 1 * TTL::SEC));
        sleep(2);
        $this->assertEquals(false, $this->cache->get($key));
    }

    public function testTtl()
    {
        $key = __METHOD__;
        $value = "test_value";
        $this->assertEquals(true, $this->cache->set($key, $value));
        $this->assertEquals(true, $this->cache->ttl($key, 2 * TTL::SEC));
        $this->assertEquals($value, $this->cache->get($key));
        sleep(4);
        $this->assertEquals(false, $this->cache->get($key));
    }

    public function testTtlNotExist()
    {
        $key = __METHOD__;
        $this->assertEquals(false, $this->cache->get($key));
        $this->assertEquals(false, $this->cache->ttl($key, 1000));
    }

    public function testGetAllDeleteAll()
    {
        $key = __METHOD__;
        $value = "test_value";
        $obj = array();
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals(true, $this->cache->set($key . $i, $value));
            $obj[$key . $i] = $value;
        }
        $this->assertEquals($obj, $this->cache->getAll());
        $this->assertEquals(true, $this->cache->deleteAll());
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals(false, $this->cache->get($key . $i));
        }
    }

    public function testDelete()
    {
        $key = __METHOD__;
        $value = "test_value";
        $this->assertEquals(true, $this->cache->set($key, $value));
        $this->assertEquals($value, $this->cache->get($key));
        $this->assertEquals(true, $this->cache->delete($key));
        $this->assertEquals(false, $this->cache->get($key));
    }

    public function testDuplicateDelete()
    {
        $key = __METHOD__;
        $value = "test_value";
        $this->assertEquals(true, $this->cache->set($key, $value));
        $this->assertEquals(true, $this->cache->delete($key));
        $this->assertEquals(false, $this->cache->delete($key));
        $this->assertEquals(true, $this->cache->deleteAll());
        $this->assertEquals(true, $this->cache->deleteAll());
    }
}
