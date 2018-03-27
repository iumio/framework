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

namespace iumioFramework\Core\Base\Container;

use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Requirement\FrameworkCore;
use iumioFramework\Core\Exception\Server\Server500;
use PHPMailer\PHPMailer\PHPMailer;


/**
 * Class FrameworkContainer
 * @package iumioFramework\Core\Base\Container
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class FrameworkContainer
{
    /** @var \DI\Container|null Container instance */
    protected static $instance = null;


    /**
     * FrameworkContainer constructor.
     * @throws Server500
     */
    protected function __construct()
    {
        $env = FEnv::get("framework.env");

        if (!in_array($env, array("dev", "prod"))) {
            throw new Server500(new \ArrayObject(
                array("Cannot create the framework container : undefined environment $env",
                    "solution" => "Please set the correct environment [dev, prod]")));
        }

        $config = FrameworkCore::getEditionInfo();
        $autowired = $config->autowiring ?? false;
        $annotation = $config->annotation ?? false;
        $builder = new \DI\ContainerBuilder();
        $builder->useAnnotations($annotation);
        $builder->useAutowiring($autowired);

        if ($env == "prod") {
            $builder = new \DI\ContainerBuilder();
            $builder->enableCompilation(FEnv::get("framework.cache").'container');
            $builder->enableDefinitionCache();
        }

        $container = $builder->build();
        self::$instance = $container;
    }



    /**
     * Get an instance of container
     *
     * @return \DI\Container Container object
     */
    public static function getInstance():\DI\Container
    {
        if (self::$instance == null) {
            new self();
        }
        return (self::$instance);
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