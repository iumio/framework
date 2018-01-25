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

/**
 * Interface SingletonInterface
 * @package iumioFramework\Core\Requirement\Pattern\Singleton
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
interface SingletonInterface
{
    /**
     * Get an instance of the class
     * @return SingletonPattern The new class instance
     */
    public static function getInstance(string $name = "none");
}