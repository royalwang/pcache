<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache;

interface Middleware
{
    public function __construct($options);
    public static function validate($options);
    public static function instanceName($options);
    public function deleteAll();
    public function get($key);
    public function set($key, $value, $ttl = TTL::INFINITY);
    public function delete($key);
    public function ttl($key, $ttl = TTL::INFINITY);
    public function getAll();
}
