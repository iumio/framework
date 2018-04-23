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

namespace iumioFramework\Core\Additional\Manager;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Requirement\Environment\FEnvInterface;

/**
 * Class FEnvFcm
 * @package iumioFramework\Core\Additional\Manager\
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class FEnvFcm extends FEnv implements FEnvInterface
{
    /** Initialize a envinronment framework paths
     * @return bool If is a success
     * @throws \Exception
     */
    public static function __initialize():bool
    {
        $env = (new ModuleManager())->getCurrentEnv();
        $base =  realpath(__DIR__.'/../../../../../../').DIRECTORY_SEPARATOR;
        self::__setPaths($base, $env, '', true);
        FEnv::set(
            "framework.fcm.config.commands.file",
            $base."vendor/iumio/iumio-framework/Core/Additional/Manager/Configs/commands.json"
        );
        return (true);
    }
}
