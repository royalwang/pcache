<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache\Middleware;

use pcache\Middleware;
use pcache\TTL;

class Memcache implements Middleware
{
    const SERVERS_KEY = "servers";

    /**
     * @var \Memcache
     */
    private $memcache;

    public function __construct(array $options)
    {
        $this->memcache = new \Memcache();
        foreach ($options[self::SERVERS_KEY] as $server) {
            $this->memcache->addServer(...$server);
        }
    }

    //==========================================================================================================//
    // Middleware Interface Functions
    //==========================================================================================================//

    public static function validate($options)
    {
    }

    public static function instanceName($options)
    {
        $servers = @$options[self::SERVERS_KEY] ?: array();
        usort($servers, function ($a, $b) {
            // FIXME: this is memcached format...=(
            if (!(is_array($a) && count($a) == 3 && is_array($b) && count($b) == 3)) {
                return 0; // invalid. So, it may be what the return value.
            }

            for ($i = 0; $i < 3; $i++) {
                if ($a[$i] == $b[$i]) {
                    continue;
                }
                return $a[$i] < $b[$i] ? -1 : 1;
            }
            return 0;
        });

        $name = "";
        foreach ($servers as $server) {
            if (!is_array($server)) {
                continue;
            }
            foreach ($server as $val) {
                $name .= strval($val);
                $name .= ":";
            }
            $name = rtrim($name, ":");
            $name .= "+";
        }
        // e.g. IP:PORT:Weight+IP:PORT:Weight
        return rtrim($name, "+");
    }

    public function setUp($options)
    {
        // NOP
    }

    public function get($key)
    {
        return $this->memcache->get($key);
    }

    public function getAll()
    {
        $ret = array();
        $items = $this->memcache->getStats("items");
        if (isset($items["items"])) {
            foreach ($items["items"] as $slabId => $item) {
                if (!($cacheDump = $this->memcache->getStats("cachedump", $slabId, $item["number"]))) {
                    continue;
                }
                foreach ($cacheDump as $key => $tmp) {
                    if (($value = $this->get($key)) !== false) {
                        $ret[$key] = $value;
                    }
                }
            }
        }
        return $ret;
    }

    public function set($key, $value, $ttl = TTL::INFINITY)
    {
        if ($ttl == TTL::INFINITY) {
            $this->memcache->set($key, $value);
        } else {
            $this->memcache->set($key, $value, 0, time() + $ttl);
        }
    }

    public function ttl($key, $ttl = TTL::INFINITY)
    {
        if (($value = $this->memcache->get($key)) !== false) {
            $this->memcache->set($key, $value, 0, ($ttl == TTL::INFINITY ? 0 : time() + $ttl));
        }
    }

    public function delete($key)
    {
        $this->memcache->delete($key);
    }

    public function deleteAll()
    {
        $this->memcache->flush();
    }
}
