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
     * @return Cache
     */
    public static function instance($middleware, $options)
    {
        static $instance = array();

        $instanceName = self::instanceName($middleware, $options);
        if (isset($instance[$middleware][$instanceName])) {
            return $instance[$middleware][$instanceName];
        } else {
            return ($instance[$middleware][$instanceName] = new self($middleware, $options));
        }
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
     * @return string[string]
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
     */
    public function set($key, $value, $ttl = TTL::INFINITY)
    {
        $this->middleware->set($key, $value, $ttl);
    }

    /**
     * update Time To Live.
     *
     * @param string $key
     * @param int $ttl
     */
    public function ttl($key, $ttl = TTL::INFINITY)
    {
        $this->middleware->ttl($key, $ttl);
    }

    /**
     * delete the key-value.
     *
     * @param string $key
     */
    public function delete($key)
    {
        $this->middleware->delete($key);
    }

    /**
     * delete all key-values.
     */
    public function deleteAll()
    {
        $this->middleware->deleteAll();
    }

    //==========================================================================================================//
    // Private Functions
    //==========================================================================================================//

    /**
     * @param $middleware
     * @return string
     */
    private static function middlewareClass($middleware)
    {
        $className = __NAMESPACE__ . '\\Middleware\\' . $middleware;
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
