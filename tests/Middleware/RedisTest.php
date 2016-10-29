<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Tests\Soranoba\Pcache\Middleware;

use Soranoba\Pcache\Cache;
use Soranoba\Pcache\Middleware\Redis;
use Tests\Soranoba\Pcache\MiddlewareTest;

class RedisTest extends MiddlewareTest
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cache = Cache::instance(Redis::class, [
            Redis::DEST_KEY => "localhost"
        ]);
    }
}
