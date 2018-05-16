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

namespace iumioFramework\Core\Additional\Manager\Module\Version;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Additional\Manager\CoreManager;
use iumioFramework\Core\Additional\Manager\Display\OutputManager as Output;
use iumioFramework\Core\Requirement\FrameworkCore;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Additional\Manager\Module\ModuleManagerInterface;
use iumioFramework\Core\Additional\Manager\FEnvFcm;

/**
 * Class VersionManager
 * @package iumioFramework\Core\Additional\Manager\Module\Version
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
        } elseif ("version:core" === $opt) {
            $this->coreVersion();
        } elseif ("version" === $opt) {
            $this->selfVersion();
        }
    }

    /**
     * Get the version informations about framework edition
     * @throws Server500
     */
    private function editionVersion()
    {
        $element = JsonListener::open(FEnvFcm::get("framework.config.core.config.file"));
        $date = new \DateTime($element->installation->date);
        $date = $date->format("Y-m-d H:i:s");
        $str = "iumio Framework ".$element->edition_fullname."\nVersion : ".
            $element->edition_version." build ".$element->edition_build."\nInstallation date : $date";
        Output::displayAsGreen($str);
    }

    /**
     * Get the version informations about framework core
     */
    private function coreVersion()
    {
        $str = "iumio Framework Core named ".FrameworkCore::CORE_NAME."\nVersion : ".
            FrameworkCore::CORE_VERSION." build ".FrameworkCore::CORE_BUILD."\nStage : ".FrameworkCore::CORE_STAGE;
        Output::displayAsGreen($str);
    }

    /**
     * Get the version informations about fcm
     * @throws Server500
     */
    private function selfVersion()
    {

        $element = JsonListener::open(FEnvFcm::get("framework.fcm.config.commands.file"));
        $str = "iumio Framework Console Manager"."\nVersion : ".
            $element->version."\nAuthors : ";
        foreach ($element->authors as $one) {
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
