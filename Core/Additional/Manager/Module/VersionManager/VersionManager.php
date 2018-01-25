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

namespace iumioFramework\Core\Console\Module\Version;

use iumioFramework\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Additionnal\Server\ServerManager as Server;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Console\CoreManager;
use iumioFramework\Core\Console\Display\OutputManager as Output;
use iumioFramework\Core\Requirement\FrameworkCore;
use iumioFramework\Exception\Server\Server500;
use iumioFramework\Core\Console\Module\ModuleManagerInterface;
use iumioFramework\Core\Console\FEnvFcm;

/**
 * Class VersionManager
 * @package iumioFramework\Core\Console\Module\Version
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class VersionManager extends ModuleManager implements ModuleManagerInterface
{

    /**
     * @return mixed|void
     * @param $options
     * @throws Server500
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;

        if (!isset($options["commands"])) {
            Output::displayAsError("Version Manager Module Error : Option is not exist. 
            Referer to help command to get options list\n");
        }

        $opt = $options["commands"][0] ?? null;
        if ("version:edition" === $opt) {
            $this->editionVersion();
        }
        else if ("version:core" === $opt) {
            $this->coreVersion();
        }
        else if ("version" === $opt) {
            $this->selfVersion();
        }

    }

    /**
     * Get the version informations about framework edition
     * @throws Server500
     */
    private function editionVersion() {
        $e = JsonListener::open(FEnvFcm::get("framework.config.core.config.file"));
        $date = new \DateTime($e->installation->date);
        $date = $date->format("Y-m-d H:i:s");
        $str = "iumio Framework ".$e->edition_fullname."\nVersion : ".
            $e->edition_version." build ".$e->edition_build."\nInstallation date : $date";
        Output::displayAsGreen($str);
    }

    /**
     * Get the version informations about framework core
     */
    private function coreVersion() {
        $str = "iumio Framework Core named ".FrameworkCore::CORE_NAME."\nVersion : ".
            FrameworkCore::CORE_VERSION." build ".FrameworkCore::CORE_BUILD."\nStage : ".FrameworkCore::CORE_STAGE;
        Output::displayAsGreen($str);
    }

    /**
     * Get the version informations about fcm
     * @throws Server500
     */
    private function selfVersion() {

        $e = JsonListener::open(FEnvFcm::get("framework.fcm.config.commands.file"));
        $str = "iumio Framework Console Manager"."\nVersion : ".
            $e->version."\nAuthors : ";
        foreach ($e->authors as $one) {
            $str.= $one->name. " <".$one->email.">\n\n";
        }
        Output::displayAsGreen($str);
    }


    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    /**
     * VersionManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("Version informations");
    }
}
