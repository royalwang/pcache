<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Soranoba\Pcache;

/**
 * TTL (Time To Live)
 * @see \Soranoba\Pcache\Cache::ttl()
 * @see \Soranoba\Pcache\Cache::set()
 */
class TTL
{
    const DAY = 86400; // 24 * self::HOUR
    const HOUR = 3600; // 60 * self::MIN
    const MIN = 60; // 60 * self::SEC
    const SEC = 1;

    const INFINITY = -1;
}
