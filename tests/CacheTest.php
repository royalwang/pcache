<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\pcache;

use pcache\Cache;
use pcache\Middleware;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    private $middleware;
    private $options;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->middleware = Middleware\SharedMemory::class;
        $this->options = [Middleware\SharedMemory::OPEN_KEY => 1111];
    }

    public function testInstance()
    {
        $instance = Cache::instance($this->middleware, $this->options);
        $this->assertEquals($instance, Cache::instance($this->middleware, $this->options));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testClone()
    {
        $instance = Cache::instance($this->middleware, $this->options);
        clone $instance;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotExistMiddleware()
    {
        Cache::instance("NOT_EXIST", []);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotMiddleware()
    {
        Cache::instance(self::class, []);
    }
}
