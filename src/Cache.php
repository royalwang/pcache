<?php
/**
 * @copyright 2016 Hinagiku Soranoba All Rights Reserved.
 */

namespace pcache;

/**
 * Cache class.
 *
 * @see \pcache\Cache::instance()
 */
class Cache
{
    /**
     * @var Middleware
     */
    private $middleware = null;

    protected function __construct($middleware, $options)
    {
        $class = self::middlewareClass($middleware);
        $class::validate($options);
        $this->middleware = new $class($options);
    }

    public function __clone()
    {
        throw new \RuntimeException(__CLASS__ . " Instance CANNOT clone");
    }

    /**
     * Return the Singleton Object.
     * @param string $middleware
     * @param array $options
     * @return Cache
     */
    public static function instance($middleware, $options)
    {
        static $instances = array();

        $instance = null;
        $instanceName = self::instanceName($middleware, $options);

        if (isset($instances[$middleware][$instanceName])) {
            $instance = $instances[$middleware][$instanceName];
        } else {
            $instance = ($instances[$middleware][$instanceName] = new self($middleware, $options));
        }

        $instance->middleware->setUp($options);
        return $instance;
    }

    /**
     * Return the Value of Key. If key does not found, return the false.
     *
     * @param string $key
     * @return string|boolean
     */
    public function get($key)
    {
        return $this->middleware->get($key);
    }

    /**
     * Return all key-values.
     * @return string[string]|bool If it is success, return all keys-values. Otherwise, return false.
     */
    public function getAll()
    {
        return $this->middleware->getAll();
    }

    /**
     * Set (insert or update) to the key-value.
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return boolean If it is success, return true. Otherwise, return false.
     */
    public function set($key, $value, $ttl = TTL::INFINITY)
    {
        return $this->middleware->set($key, $value, $ttl);
    }

    /**
     * update Time To Live.
     *
     * @param string $key
     * @param int $ttl
     * @return boolean If it is success, return true. Otherwise, return false.
     */
    public function ttl($key, $ttl = TTL::INFINITY)
    {
        return $this->middleware->ttl($key, $ttl);
    }

    /**
     * delete the key-value.
     *
     * @param string $key
     * @return boolean If it is success, return true. Otherwise (e.g. the key doesn't exist), return false.
     */
    public function delete($key)
    {
        return $this->middleware->delete($key);
    }

    /**
     * delete all key-values.
     * @return boolean If it is success, return true. Otherwise, return false.
     */
    public function deleteAll()
    {
        return $this->middleware->deleteAll();
    }

    //==========================================================================================================//
    // Private Functions
    //==========================================================================================================//

    /**
     * validate the class.
     *
     * @param string $className
     * @return string
     */
    private static function middlewareClass($className)
    {
        if (!class_exists($className)) {
            throw new \RuntimeException($className . " is not found");
        }
        if (!in_array(Middleware::class, class_implements($className))) {
            throw new \RuntimeException($className . " doesn't implement the interface of " . Middleware::class);
        }
        return $className;
    }

    /**
     * Return the Instance Name.
     * If an instance of the same name already have been created, it is turned to use as the same instance.
     *
     * @return string
     */
    private static function instanceName($middleware, $options)
    {
        $class = self::middlewareClass($middleware);
        return $class::instanceName($options);
    }
}
