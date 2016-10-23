<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache;

/**
 * TTL (Time To Live)
 * @see \pcache\Cache::ttl()
 * @see \pcache\Cache::set()
 */
class TTL
{
    const DAY = 24 * self::HOUR;
    const HOUR = 60 * self::MIN;
    const MIN = 60 * self::SEC;
    const SEC = 1;

    const INFINITY = -1;
}
