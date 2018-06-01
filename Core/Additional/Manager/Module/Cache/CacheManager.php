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

namespace iumioFramework\Core\Additional\Manager\Module\Cache;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Server\Server as Server;
use iumioFramework\Core\Additional\Manager\CoreManager;
use iumioFramework\Core\Additional\Manager\Display\OutputManager as Output;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Additional\Manager\Module\ModuleManagerInterface;
use iumioFramework\Core\Additional\Manager\FEnvFcm;

/**
 * Class CacheManager
 * @package iumioFramework\Core\Additional\Manager\Module\Cache
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class CacheManager extends ModuleManager implements ModuleManagerInterface
{
    protected $options;

    /**
     * @return mixed|void
     * @param $options
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;
        if (!isset($options["commands"])) {
            Output::displayAsError("Cache Manager Module Error : Option is not exist. 
            Referer to help command to get options list\n");
        }

        $opt = $options["commands"][0] ?? null;
        if ($opt == "cache:clear") {
            if (empty($options["options"])) {
                $this->deleteCache("dev");
            } elseif (in_array("--env=dev", $options["options"])) {
                $this->deleteCache("dev");
            } elseif (in_array("--env=prod", $options["options"])) {
                $this->deleteCache("prod");
            } elseif (in_array("--env=all", $options["options"])) {
                $this->deleteAllCache();
            } else {
                Output::displayAsError("Cache Manager Module Error : Bad option\n");
            }
        } else {
            Output::displayAsError("Cache Manager Module Error : Option is not exist. 
            Referer to help command to get options list\n");
        }
    }

    /** Delete a cache from a specific environment
     * @param string $env Environment name
     * @throws Server500
     * @throws \Exception
     */
    private function deleteCache(string $env)
    {
        Output::displayAsSuccess("Hey, I delete cache from $env environment ", "none");
        $this->callDelCreaServer($env);
        if (in_array("--noexit", $this->options["options"])) {
            Output::displayAsEndSuccess("Cache delete for $env environment is successfull.", "none");
        } else {
            Output::displayAsNormal("Cache delete for $env environment is successfull.");
        }
    }

    /** Call Server delete and create function
     * @param string $env Environment name
     * @throws Server500
     * @throws \Exception
     */
    private function callDelCreaServer(string $env)
    {
        Server::delete(FEnvFcm::get("framework.cache").$env, 'directory');
        Server::create(FEnvFcm::get("framework.cache").$env, 'directory');
    }

    /**
     * Delete a cache for all environment
     * @throws Server500
     * @throws \Exception
     */
    private function deleteAllCache()
    {
        $a = array("dev", "prod");
        Output::displayAsSuccess("Hey, I delete cache for all environment", "none");
        for ($i = 0; $i < count($a); $i++) {
            $this->callDelCreaServer($a[$i]);
        }
        if (in_array("--noexit", $this->options["options"])) {
            Output::displayAsEndSuccess("Cache was deleted for all environment.", "none");
        } else {
            Output::displayAsNormal("Cache was deleted for all environment.");
        }
    }


    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    public function __construct()
    {
        CoreManager::setCurrentModule("Cache Manager");
    }
}
