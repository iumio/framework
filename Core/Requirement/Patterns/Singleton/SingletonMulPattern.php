<?php

/**
 **
 ** This is an iumio Framework component
 **
 ** (c) RAFINA DANY <dany.rafina@iumio.com>
 **
 ** iumio Framework, an iumio component [https://iumio.com]
 **
 ** To get more information about licence, please check the licence file
 **
 **/

namespace iumioFramework\Core\Requirement\Patterns\Singleton;
use iumioFramework\Exception\Server\Server500;


/**
 * Class SingletonMulPattern
 * This class contain multiple instance instead of SingletonClassicPattern
 * @package iumioFramework\Core\Requirement\Pattern\Singleton
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 *
 */
abstract class SingletonMulPattern
{
    /** @var array List of Class instances */
    protected static $instances = [];

    /**
     * Get an instance of the class
     * @param $name string Class name
     * Name must be the class name
     * @return mixed The new class instance
     */
    public static function getInstance(string $name)
    {
        if (!isset(self::$instances[$name]) || empty(self::$instances[$name])) {
            new static();
        }
        return (self::$instances[$name]);
    }

    /** Set a new instance in instance mapping
     * @param string $name Instance name
     * @param mixed $object Instance object
     * @return bool True if instance has been setted in instance mapping
     * @throws Server500 If instance has been redeclared
     */
    protected function setInstance(string $name, $object):bool
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = $object;
            return (true);
        }
        throw new Server500(new \ArrayObject(array("explain" => "Cannot redeclare instance name $name as new instance",
            "solution" => "Please use SingletonMulPattern correctly")));
    }

    /** Check if instance is defined
     * @param string $name Instance name
     * @return bool Defined or not
     */
    protected function issetInstance(string $name):bool
    {
        return (isset(self::$instances[$name]));
    }


    /**
     * Clone method has private to prevent the cloning instance
     * @return null
     */
    final private function __clone()
    {
        // LOCKED THE CLONE
        return (null);
    }
    /**
     * is declared as private to prevent unserializing
     * of an instance of the class via the global function unserialize()
     * @return null
     */
    final private function __wakeup()
    {
        // LOCKED THE UNSERIALIZE
        return (null);
    }

}