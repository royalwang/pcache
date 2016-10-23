<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache\Middleware;

use pcache\Middleware;
use pcache\TTL;

/**
 * Class Redis
 *
 * @see \pcache\Cache::instance()
 *
 * options:
 *    dest    (require)  string  : Hostname, ip or unix domain socket path.
 *    port    (optional) integer : port number.
 *    timeout (optional) number  : connection timeout (sec)
 */
class Redis implements Middleware
{
    const DEST_KEY = "dest";
    const PORT_KEY = "port";
    const TIMEOUT_KEY = "timeout";

    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(array $options)
    {
        $this->redis = new \Redis();
        $host = $options[self::DEST_KEY];
        $port = isset($options[self::PORT_KEY]) ? $options[self::PORT_KEY] : ($host[0] == "/" ? 0 : 6379);
        $timeout = isset($options[self::TIMEOUT_KEY]) ? $options[self::TIMEOUT_KEY] : 0.0;
        if (!$this->redis->pconnect($host, $port, $timeout)) {
            throw new \Exception("Redis connection failed");
        }
    }

    //==========================================================================================================//
    // Middleware Interface Functions
    //==========================================================================================================//

    public static function validate($options)
    {
        if (!isset($options[self::DEST_KEY])) {
            throw new \RuntimeException(__CLASS__ . " MUST need the option of " . self::DEST_KEY);
        }
        if (!is_string($options[self::DEST_KEY])) {
            throw new \RuntimeException("");
        }
        if (isset($options[self::PORT_KEY]) && !is_int($options[self::PORT_KEY])) {
            throw new \RuntimeException("");
        }
        if (isset($options[self::PORT_KEY])
            && !($options[self::PORT_KEY] > 0 && $options[self::PORT_KEY] <= 65535)) {
            throw new \RuntimeException("");
        }
        if (isset($options[self::TIMEOUT_KEY]) && !is_numeric($options[self::TIMEOUT_KEY])) {
            throw new \RuntimeException("");
        }
    }

    public static function instanceName($options)
    {
        $key = @$options[self::DEST_KEY];
        $port = @$options[self::PORT_KEY];
        $timeout = @$options[self::TIMEOUT_KEY];

        return "{$key}:{$port}:{$timeout}";
    }

    public function setUp($options)
    {
        // NOP
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function getAll()
    {
        $ret = array();
        foreach ($this->redis->getKeys("*") as $key) {
            if ($value = $this->get($key)) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    public function set($key, $value, $ttl = TTL::INFINITY)
    {
        if ($ttl == TTL::INFINITY) {
            $this->redis->set($key, $value);
        } else {
            $this->redis->set($key, $value, $ttl);
        }
    }

    public function ttl($key, $ttl = TTL::INFINITY)
    {
        $this->redis->setTimeout($key, $ttl);
    }

    public function delete($key)
    {
        $this->redis->delete($key);
    }

    public function deleteAll()
    {
        $this->redis->flushAll();
    }
}
