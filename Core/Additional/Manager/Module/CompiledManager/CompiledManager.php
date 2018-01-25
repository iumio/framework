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

namespace iumioFramework\Core\Console\Module\Compiled;

use iumioFramework\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Additionnal\Server\ServerManager as Server;
use iumioFramework\Core\Console\CoreManager;
use iumioFramework\Core\Console\Display\OutputManager as Output;
use iumioFramework\Core\Console\Module\ModuleManagerInterface;
use iumioFramework\Core\Console\FEnvFcm;
use iumioFramework\Exception\Server\Server500;

/**
 * Class CompiledManager
 * @package iumioFramework\Core\Console\Module\Compiled
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class CompiledManager extends ModuleManager implements ModuleManagerInterface
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
            Output::displayAsError("Compiled Manager Module Error : Option is not exist. 
            Referer to help command to get options list\n");
        }

        $opt = $options["commands"][0] ?? null;
        if ($opt == "compiled:clear") {
            if (empty($options["options"])) {
                $this->deleteCompiled("dev");
            } elseif (in_array("--env=dev", $options["options"])) {
                $this->deleteCompiled("dev", "yes");
            } elseif (in_array("--env=prod", $options["options"])) {
                $this->deleteCompiled("prod", "yes");
            } elseif (in_array("--env=all", $options["options"])) {
                $this->deleteAllCompiled();
            } else {
                Output::displayAsError("Compiled Manager Module Error : Bad option\n");
            }
        } else {
            Output::displayAsError("Compiled Manager Module Error : Option is not exist. 
            Referer to help command to get options list\n");
        }
    }

    /** Delete a Compiled from a specific environment
     * @param string $env Environment name
     * @param string $isdefault If no environment option
     * @throws Server500
     * @throws \Exception
     */
    private function deleteCompiled(string $env, string $isdefault = null)
    {
        Output::displayAsSuccess("Hey, I delete compiled from $env environment ", "none");
        $this->callDelCreaServer($env);
        if (in_array("--noexit", $this->options["options"])) {
            Output::displayAsEndSuccess("Compiled delete for $env environment(s) is successfull.", "none");
        } else {
            Output::displayAsNormal("Compiled delete for $env environment(s) is successfull.");
        }

    }

    /** Call Server delete and create function
     * @param string $env Environment name
     * @throws Server500
     * @throws \Exception
     */
    private function callDelCreaServer(string $env)
    {
        Server::delete(FEnvFcm::get("framework.compiled").$env, 'directory');
        Server::create(FEnvFcm::get("framework.compiled").$env, 'directory');
    }

    /**
     * Delete a compiled for all environment
     * @throws Server500
     * @throws \Exception
     */
    private function deleteAllCompiled()
    {
        $a = array("dev", "prod");
        Output::displayAsSuccess("Hey, I delete compiled for all environment", "none");
        for ($i = 0; $i < count($a); $i++) {
            $this->callDelCreaServer($a[$i]);
        }
        if (in_array("--noexit", $this->options["options"])) {
            Output::displayAsEndSuccess("Compiled was deleted for all environment.", "none");
        } else {
            Output::displayAsNormal("Compiled was deleted for all environment.");
        }
    }



    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    /**
     * CompiledManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("Compiled Manager");
    }
}
