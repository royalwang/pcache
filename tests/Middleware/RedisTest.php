<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\pcache\Middleware;

use pcache\Cache;
use pcache\Middleware\Redis;
use Tests\pcache\MiddlewareTest;

class RedisTest extends MiddlewareTest
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::instance("Redis", [
            Redis::DEST_KEY => "localhost"
        ]);
    }
}
