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

namespace iumioFramework\Core\Additional\Manager\Module;

use iumioFramework\Core\Additional\Manager\ComManager;
use iumioFramework\Core\Exception\Server\Server500;

/**
 * Class PredictionManager
 * @package iumioFramework\Core\Manager\Module
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class PredictionManager
{

    /**
     * Parse commands and options on console
     * @param string $name command name
     * @return array matches commands
     * @throws Server500 If $arguments array cannot have some commands
     */
    public static function parsePredict(string $name):array
    {
        $f = ComManager::getFileCommand();
        $meaning = [];
        if ($f == null) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Framework Predictions Error : Command File is empty",
                "solution" => "Command file cannot not be empty")));
        }
        $commands = $f->commands;

        foreach ($commands as $command => $val) {
            if ($val->type === "single") {
                if (stristr($command, $name) != false) {
                    $meaning[] = $commands;
                }
            } elseif ($val->type === "multiple") {
                foreach ($val->args as $acommand => $aval) {
                    if (stristr($aval->name, $name) != false) {
                        $meaning[] = $aval->name;
                    }
                }
            }
        }
        return ($meaning);
    }

    /**
     * Struct message with "Did you mean" message
     * @param array $meaning predictive result
     * @return string
     */
    public static function structMessage(array $meaning):string
    {
        $str = "";
        if (empty($meaning)) {
            return "";
        }
        foreach ($meaning as $one) {
            $str .= "  ".$one."\n\n";
        }
        $str = "Did you mean one of these ? :\n\n".$str;
        return ($str);
    }
}
