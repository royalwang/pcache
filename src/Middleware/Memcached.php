<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace Soranoba\Pcache\Middleware;

use Soranoba\Pcache\Middleware;
use Soranoba\Pcache\TTL;

/**
 * Class Memcached
 *
 * @see \Soranoba\Pcache\Cache::instance()
 *
 * options:
 *    servers (require)  array : list of array(hostname(string), port(integer))
 */
class Memcached implements Middleware
{
    const SERVERS_KEY = "servers";

    /**
     * @var \Memcached
     */
    private $memcached;

    public function __construct(array $options)
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServers($options[self::SERVERS_KEY]);
    }

    //==========================================================================================================//
    // Middleware Interface Functions
    //==========================================================================================================//

    public static function validate($options)
    {
    }

    public static function instanceName($options)
    {
        $name = "";
        $servers = @$options[self::SERVERS_KEY];
        if (!is_array($servers)) {
            return $name;
        }

        usort($servers, function ($a, $b) {
            for ($i = 0; $i < min(count($a), count($b)); $i++) {
                if ($a[$i] == $b[$i]) {
                    continue;
                }
                return $a[$i] < $b[$i] ? -1 : 1;
            }
            return 0;
        });

        foreach ($servers as $server) {
            if (!is_array($server)) {
                continue;
            }
            for ($i = 0; $i < 2; $i++) { // ip & port
                $name .= strval($server[$i]);
                $name .= ":";
            }
            $name = rtrim($name, ":");
            $name .= "+";
        }
        // e.g. IP:PORT+IP:PORT
        return rtrim($name, "+");
    }

    public function setUp($options)
    {
        // NOP
    }

    public function get($key)
    {
        return $this->memcached->get($key);
    }

    public function getAll()
    {
        $keys = $this->getAllKeys() ?: array();
        $ret = array();
        foreach ($keys as $key) {
            if (($value = $this->get($key)) !== false) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    public function set($key, $value, $ttl = TTL::INFINITY)
    {
        if ($ttl == TTL::INFINITY) {
            return $this->memcached->set($key, $value);
        } else {
            return $this->memcached->set($key, $value, time() + $ttl);
        }
    }

    public function ttl($key, $ttl = TTL::INFINITY)
    {
        if (($value = $this->memcached->get($key)) !== false) {
            return $this->memcached->set($key, $value, ($ttl == TTL::INFINITY ? 0 : time() + $ttl));
        }
        return false;
    }

    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    public function deleteAll()
    {
        if (($keys = $this->getAllKeys()) !== false) {
            $count = 0;
            foreach (($this->memcached->deleteMulti($keys) ?: array()) as $key => $value) {
                $count += (int)$value; // bool to integer
            }
            return count($keys) == $count;
        }
        return false;
    }

    //==========================================================================================================//
    // Private Functions
    //==========================================================================================================//

    /**
     * Return stored all keys on Memcached
     *
     * NOTE: \Memcached::getAllKeys has bug. So, it has prepared an alternative method.
     *       https://github.com/php-memcached-dev/php-memcached/issues/203
     *
     * @return array|bool If it is success, return keys. Otherwise, return false.
     */
    private function getAllKeys()
    {
        $servers = $this->memcached->getServerList();
        if (($fp = @fsockopen($servers[0]["host"], $servers[0]["port"])) === false) {
            return false;
        }
        if (($items = self::exec($fp, "stats items")) === false) {
            return false;
        }
        $keys = array();
        foreach ($items as $item) {
            if (preg_match("/^STAT items:(\d+)\:number (\d+)$/", $item, $matches)) {
                if ($dumps = self::exec($fp, "stats cachedump {$matches[1]} {$matches[2]}")) {
                    foreach ($dumps as $dump) {
                        if (preg_match("/^ITEM ([^ ]+) .*$/", $dump, $matches)) {
                            $keys[] = $matches[1];
                        }
                    }
                }
            }
        }
        fclose($fp);
        return $keys;
    }

    /**
     * Write the memcached cmd on socket, and get the response.
     *
     * @param $fp resource A file system pointer resource that is typically created using fopen().
     * @param $cmd string command string that you want to execute.
     * @return string[]|bool Returns the response split with a newline. If the cmd failed, return false.
     */
    private static function exec($fp, $cmd)
    {
        if (!fwrite($fp, $cmd . "\n")) {
            return false;
        }
        $ret = array();
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            if ($line == "END") {
                break;
            } elseif (strpos($line, "ERROR") === 0) {
                break;
            }
            $ret[] = $line;
        }
        return $ret;
    }
}
