<?php

/**
 *
 *  * This is an iumio Framework component
 *  *
 *  * (c) RAFINA DANY <danyrafina@iumio.com>
 *  *
 *  * iumio Framework, an iumio component [https://iumio.com]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

namespace iumioFramework\Core\Additional\Manager;

use iumioFramework\Core\Additional\Manager\Module\ParseManager;
use iumioFramework\Core\Additional\Manager\Module\PredictionManager;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\Reflection\FrameworkReflection as Reflex;
use iumioFramework\Core\Additional\Manager\ComManager as File;
use iumioFramework\Core\Additional\Manager\Display\OutputManager as Output;

/**
 * Class ArgsManager
 * @package iumioFramework\Core\Additional\Manager\
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class ArgsManager
{

    static public $option = array();
    protected $fileCommand = null;

    /**
     * ArgsManager constructor.
     */
    public function __construct()
    {
        $this->fileCommand = File::getFileCommand();
    }

    /** Get prompt arguments
     * @param int $argc Argument number
     * @param array $argv Arguments
     * @throws \Exception
     */
    public function getArgs(int $argc, array $argv)
    {
        if ($argc == 1) {
            Output::displayAsGreen(
                "Welcome to the Framework Console Manager\n".
                "I noticed that you didn't enter any parameters.\n".
                "For more information, you can use the help command to get a command list."
            );
        }

        $c = $this->searchCommand($argv[1]);
        if (empty($c)) {
            CoreManager::setCurrentModule("Error");
            $predict = (PredictionManager::parsePredict($argv[1]));
            if (empty($predict)) {
                Output::displayAsError("Command not found.\n".
                    "For more information, you can use the [help] command to get a command list.");
            } else {
                Output::displayAsError("Input [".$argv[1]."] is ambigus.\n".
                    "To get command list, use [help] keyword.\n\n".
                    PredictionManager::structMessage($predict));
            }
        }

        $p = ParseManager::parse($argv);
        $ref = new Reflex();
        $ref->__simple($c['class'], $p);
    }


    /** Search a command name
     * @param string $name Command name
     * @return array Search result as an array
     * @throws \Exception Exception will generate of is an empty file
     */
    protected function searchCommand(string $name):array
    {
        $f = $this->fileCommand;
        $finalC = array();
        if ($f == null) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Framework Console Arguments Error : Command File is empty",
                "solution" => "Command file not be empty")));
        }
        $commands = $f->commands;

        foreach ($commands as $command => $val) {
            if ($val->type === "single") {
                if ($command == $name) {
                    return (array("name" => $command, "class" => $val->class, "desc" => $val->desc));
                }
            } elseif ($val->type === "multiple") {
                foreach ($val->args as $acommand => $aval) {
                    if ($aval->name === $name) {
                        return (array("name" => $aval->name, "class" => $val->class, "desc" => $aval->desc));
                    }
                }
            } else {
                throw new Server500(new \ArrayObject(array("explain" => "Undefined command type [".$val->type."]",
                    "solution" => "Please set a valid command type [single, multiple]")));
            }
        }
        return ($finalC);
    }
}
