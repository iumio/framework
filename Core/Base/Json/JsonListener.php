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

namespace iumioFramework\Core\Base\Json;

use iumioFramework\Core\Server\Server;
use iumioFramework\Core\Exception\Server\Server500;

/**
 * Class JsonListener
 * @package iumioFramework\Core\Base\Json
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class JsonListener implements JsonInterface
{
    static private $file = null;
    static private $filepath = null;

    /** Open file configuration
     * @param $filepath string Filepath
     * @return \stdClass File content
     * @throws 5Server500 If file does not exist or not readable
     */
    public static function open(string $filepath):\stdClass
    {
        if ($filepath == self::$filepath && self::$file != null) {
            return (self::$file);
        }

        if (!file_exists($filepath)) {
            throw new Server500(new \ArrayObject(array("explain" => "Cannot open file $filepath : File does not exit",
                "solution" => "Please set the correct filepath")));
        }

        if (!is_readable($filepath)) {
            throw new Server500(new \ArrayObject(array("explain" => "Cannot open file $filepath : File not readable",
                "solution" => "Please set the correct permission")));
        }

        $a = json_decode(file_get_contents($filepath));

        self::$file = ($a == null ? new \stdClass() : $a);
        return ($a == null ? new \stdClass() : $a);
    }

    /** Put content in configuration file
     * @param $filepath string File path
     * @param $content string new file content
     * @return int success
     * @throws
     */
    public static function put(string $filepath, string $content):int
    {
        if (!file_exists($filepath)) {
            Server::create($filepath, "file");
        }
        file_put_contents($filepath, $content);
        self::open($filepath);
        return (1);
    }

    /** Check if json file exist
     * @param string ...$filepath File path(s)
     * @return array|bool  A boolean array with key and value (file path and if it exist or not) | if file exist or not
     */
    public static function exists(string ...$filepath)
    {
        if (count($filepath) == 1) {
            return (file_exists($filepath[0]));
        } else {
            $exists = [];
            foreach ($filepath as $one) {
                array_push($exists, [$one => file_exists($one)]);
            }
            return ($exists);
        }
    }

    /** Close file configuration
     * @param string $filepath File path
     * @return int Is file closed
     */
    public static function close(string $filepath):int
    {
        if (self::$file != null) {
            self::$filepath = null;
            self::$file = null;
        }
        return (1);
    }
}
