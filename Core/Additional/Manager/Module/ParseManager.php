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

use iumioFramework\Core\Exception\Server\Server500;

/**
 * Class ParseManager
 * @package iumioFramework\Core\Manager\Module
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class ParseManager
{

    /**
     * Parse commands and options on console
     * @param array $arguments Console elements
     * @return array An array with commands and options
     * @throws Server500 If $arguments array cannot have some commands
     */
    public static function parse(array $arguments):array
    {
        if (count($arguments) < 1) {
            throw new Server500(new \ArrayObject(array("explain" => "Cannot parse value without commands",
                "solution" => "Please set an command to parse it")));
        }
        $exec = self::parseExecutable($arguments);

        if (isset($arguments[0])) {
            unset($arguments[0]);
        }

        $com = self::parseCommands($arguments);
        $opt = self::parseOptions($arguments);

        return (["global" => $exec, "commands" => $com, "options" => $opt]);
    }


    /**
     * Parse commands on console
     * @param array $arguments All element in console
     * @return array The commands on console
     */
    private static function parseCommands(array $arguments):array
    {
        $commands = [];
        foreach ($arguments as $one) {
            if (strpos($one, '--') === false) {
                $commands[] = $one;
            }
        }
        return ($commands);
    }

    /**
     * Parse executable and manager path on console
     * @param array $arguments All element in console
     * @return array executable and manager path on console in array
     */
    private static function parseExecutable(array $arguments):array
    {
        return (["executable" => 'php', "manager" => $arguments[0]]);
    }


    /**
     * Parse options on console
     * @param array $arguments All element in console
     * @return array The options on console
     */
    private static function parseOptions(array $arguments):array
    {
        $options = [];
        foreach ($arguments as $one) {
            if (strpos($one, '--') !== false && strlen($one) > 2) {
                $options[] = $one;
            }
        }
        return ($options);
    }
}
