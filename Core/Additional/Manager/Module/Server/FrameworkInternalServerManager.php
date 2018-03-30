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

namespace iumioFramework\Core\Additional\Manager\Module\Server;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Additional\Manager\CoreManager;
use iumioFramework\Core\Additional\Manager\Display\OutputManager as Output;
use iumioFramework\Core\Additional\Manager\Module\ModuleManagerInterface;
use iumioFramework\Fis\Runner;

/**
 * Class FrameworkInternalServerManager
 * @package iumioFramework\Core\Additional\Manager\Module\Server
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class FrameworkInternalServerManager extends ModuleManager implements ModuleManagerInterface
{
    protected $options;

    /**
     * Run the development server without options
     * @throws \iumioFramework\Core\Exception\Server\Server500
     */
    private function runServer()
    {
        if ($this->getCurrentEnv() === "dev") {
            if (empty($this->options["options"])) {
                Output::displayAsGreen("Running the Framework Inernal Server on http://localhost:8000", "none");
                $a = new Runner();
                $a->run();
            }
            else {
                $host = null;
                $port = null;
                $secure = false;
                $root = null;
                $router = null;
                $cert = null;
                $cluster = 10;
                if (($a = $this->strlikeInArray("--host", $this->options["options"])) != null) {
                    $host = (explode("=", $a))[1];
                }

                if (($a = $this->strlikeInArray("--port", $this->options["options"])) != null) {
                    $port = (explode("=", $a))[1];
                }

                if (($a = $this->strlikeInArray("--secure", $this->options["options"])) != null) {
                    $secure = true;
                }

                if (($a = $this->strlikeInArray("--root", $this->options["options"])) != null) {
                    $root = (explode("=", $a))[1];
                }

                if (($a = $this->strlikeInArray("--router", $this->options["options"])) != null) {
                    $router = (explode("=", $a))[1];
                }

                if (($a = $this->strlikeInArray("--cert", $this->options["options"])) != null) {
                   if ($secure) {
                       $cert = (explode("=", $a))[1];
                   }

                else {
                        Output::displayAsRed("Fis Error : Cannot use certificate without a secure connection.\n
                     Please use the option --secure to use a certificate");
                    }
                }

                if (($a = $this->strlikeInArray("--cluster", $this->options["options"]) != null)) {
                    $cluster = (explode("=", $a))[1];
                }

                Output::displayAsGreen("Running the Framework Internal Server on ".
                    (($secure)? "https" : "http")."://".((is_null($host))? "localhost" : $host).
                    ":".((is_null($port))? "8000" : $port)."", "none");
                $a = new Runner();
                $a->run($host, $port, $secure, $root, $router, $cert, $cluster);
            }
        }
        else {
            Output::displayAsError("Fis Manager : Cannot run development server when production 
            environment was settled by default.\n Please set development environment to use server.");
        }
    }

    /**
     * @return mixed|void
     * @param $options
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;
        if (!isset($options["commands"])) {
            Output::displayAsError("Fis Manager Error : Option is not exist. 
            Referer to help command to get options list\n");
        }

        $opt = $options["commands"][0] ?? null;
        if ("server:start" == $opt) {
            $this->runServer();
        } else {
            Output::displayAsError("Fis Manager Error : Option is not exist. 
            Referer to help command to get options list\n");
        }
    }

    public function __alter()
    {
        // TODO: Implement __alter() method.
    }


    /**
     * HelpManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("Framework internal server Manager");
    }
}
