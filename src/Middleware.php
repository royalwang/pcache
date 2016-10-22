<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache;

/**
 * Middleware interface of \pcache\Cache
 */
interface Middleware
{
    /**
     * Middleware constructor.
     * @param array $options
     */
    public function __construct(array $options);

    /**
     * Validate the options. If it is invalid, throw the Exception.
     * @param array $options
     */
    public static function validate($options);

    /**
     * Return the instance name.
     * If an instance of the same name already have been created, it is turned to use as the same instance.
     *
     * @param array $options
     * @return string
     */
    public static function instanceName($options);

    /**
     * Return the Value of Key. If key does not found, return the false.
     *
     * @param string $key
     * @return string|boolean
     */
    public function get($key);

    /**
     * Return all key-values.
     * @return string[string]
     */
    public function getAll();

    /**
     * Set (insert or update) to the key-value.
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     */
    public function set($key, $value, $ttl = TTL::INFINITY);

    /**
     * update Time To Live.
     *
     * @param string $key
     * @param int $ttl
     */
    public function ttl($key, $ttl = TTL::INFINITY);

    /**
     * delete the key-value.
     *
     * @param string $key
     */
    public function delete($key);

    /**
     * delete all key-values.
     */
    public function deleteAll();
}
