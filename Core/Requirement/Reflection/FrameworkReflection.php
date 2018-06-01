<?php

/**
 *
 *  * This is an iumio Framework component
 *  *
 *  * (c) RAFINA DANY <dany.rafina@iumio.com>
 *  *
 *  * iumio Framework, an iumio component [https://iumio.com]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

namespace iumioFramework\Core\Requirement\Reflection;

use iumioFramework\Core\Base\Container\FrameworkContainer;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Requirement\FrameworkServices\FrameworkTools;

/**
 * Class FrameworkReflection
 * @package iumioFramework\Core\Requirement\Relexion
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class FrameworkReflection
{

    /**
     * Pass method arguments by name
     * @param string $class Class name
     * @param string $method $method
     * @param array $args Method parameters
     * @return mixed
     * @throws \Exception
     */

    public function __named(string $class, string $method, array $args = array())
    {
        FEnv::set("app.activity_called", array("class" => $class, "method" => $method));
        $container = FrameworkContainer::getInstance();
        $call = $container->get($class);
       
        $rs = $container->call($class."::".$method, $args);
        return ($rs);
    }


    /**
     * Create a simple instance
     * @param string $class Class Name
     * @param array $args Constructor parameters
     * @throws \Exception
     */
    public function __simple(string $class, array $args)
    {
        $config = FrameworkTools::getEditionInfo();
        $autowired = $config->autowiring ?? false;
        $annotation = $config->annotation ?? false;
        $container = FrameworkContainer::getInstance();
        $builder = new \DI\ContainerBuilder();
        $builder->useAnnotations($annotation);
        $builder->useAutowiring($autowired);
        $container = $builder->build();
        $call = $container->get($class);
        try {
            $rs = $container->call($class."::__render", array($args));
        } catch (\RuntimeException $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /** Create a simple instance
     * @param string $class Class Name
     * @param array $args Constructor parameters
     * @return mixed Return class instance
     * @throws \Exception
     */
    public function __simpleReturned(string $class, array $args = array())
    {
        try {
            $class = new \ReflectionClass($class);
            if (empty($args)) {
                return ($class->newInstanceArgs());
            } else {
                return ($class->newInstanceArgs(array($args)));
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
}
