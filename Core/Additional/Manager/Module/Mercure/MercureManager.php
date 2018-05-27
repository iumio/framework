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

namespace iumioFramework\Core\Additional\Manager\Module\Mercure;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Additional\Manager\CoreManager;
use iumioFramework\Core\Additional\Manager\Display\OutputManager as Output;
use iumioFramework\Core\Additional\Manager\Module\ModuleManagerInterface;
use iumioFramework\Core\Routing\Js\JsRouting;

/**
 * Class MercureManager
 * @package iumioFramework\Core\Additional\Manager\Module\Cache
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class MercureManager extends ModuleManager implements ModuleManagerInterface
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
            Output::displayAsError("Version Manager Module Error : Option is not exist. 
            Referer to help command to get options list \n");
        }

        $opt = $options["commands"][0] ?? null;
        $subs = (empty($options["options"])? [] : $options["options"]);
        if (empty($this->options)) {
            Output::displayAsError("Mercure Manager Module Error : You must to specify an option\n");
        }
        if ($opt == "mercure:build:jsrouting") {
            $this->buildJsRouting(((in_array("--baseapp", $subs)? true : false)));
        } else {
            Output::displayAsError("Mercure Manager Module Error : Option is not exist.
             Referer to help command to get options list\n");
        }
    }

    /** Build the JS Routing file
     * @param bool $isbaseapp If a base app
     */
    private function buildJsRouting(bool $isbaseapp = false)
    {
        Output::displayAsSuccess("Hey, I will build the JS Routing file", "none");
        $rt = new JsRouting($isbaseapp);
        $rt->build();
        if (in_array("--noexit", $this->options["options"])) {
            Output::displayAsEndSuccess("Build the JS Routing File is a success.", "none");
        } else {
            Output::displayAsNormal("Build the JS Routing File is a success.");
        }
    }

    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    public function __construct()
    {
        CoreManager::setCurrentModule("Mercure Manager (Routing)");
    }
}
