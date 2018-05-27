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

namespace iumioFramework\Core\Requirement\FrameworkServices;

use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Exception\Server\Server000;

/**
 * Class AppTools
 * @package iumioFramework\Core\Requirement\FrameworkServices
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class AppTools
{

    /** Get real app name
     * @param string $fullAppName Full app name
     * @return string the real app name
     */
    public static function getRealAppName(string $fullAppName):string
    {
        return (substr($fullAppName, 0, (strlen($fullAppName) - 3)));
    }

    /** Merge two simple array to key/value array
     * @param array $keys array with all key
     * @param array $values value array
     * @return mixed Merged array or false
     */
    public static function assembly(array $keys, array $values)
    {
        if (count($keys) !== count($values)) {
            return (false);
        }
        return (array_combine($keys, $values));
    }


    /** Get all simple app
     * @param array $apps App list
     * @return array getSimpleAppFormat
     */
    public static function getSimpleAppFormat(array $apps):array
    {
        $narray = array();
        foreach ($apps as $oneapp => $val) {
            $e = AppConfig::getInstance($oneapp);
            if ($e->checkVisibility()) {
                array_push($narray, array("name" => $oneapp, "value" => $val));
            }
        }
        return ($narray);
    }

    /** Detect the default app
     * @param array $apps App list
     * @return array The default app
     * @deprecated  Will remove next major release
     * @throws \Exception When does not have a default app
     */
    public static function detectDefaultApp(array $apps):array
    {
        foreach ($apps as $oneapp => $val) {
            if ($val['isdefault'] == "yes") {
                return (array("name" => $oneapp, "value" => $val));
            }
        }

        throw new Server500(new \ArrayObject(array("explain" => "No Default app is detected", "solution" =>
            "Please edit apps.json to set a default app")));
    }

    /** Return app declaration file
     * @return \stdClass File result
     * @throws
     */
    final public static function getClassFile():\stdClass
    {
        $a = json_decode(file_get_contents(FEnv::get("framework.config.core.apps.file")));
        return ($a == null ? new \stdClass() : $a);
    }

    /** Return base app declaration file
     * @return \stdClass File result
     * @throws
     */
    final public static function getBaseClassFile():\stdClass
    {
        $a = json_decode(file_get_contents(FEnv::get("framework.baseapps.apps.file")));
        return ($a == null ? new \stdClass() : $a);
    }


    /** Get all app register on apps.json
     * @return array Apps register
     * @throws Server000
     */

    public static function registerApps():array
    {
        $classes = self::getClassFile();
        if (count((array)$classes) == 0) {
            throw new Server000(new \ArrayObject(array()));
        }
        $apps = array();
        foreach ($classes as $class => $val) {
            $val = (array)$val;
            $apps[$val['name']] =  array("appclass" => new $val['class'](),
                "enabled" => $val['enabled'], "prefix" => $val['prefix']);
        }
        return $apps;
    }

    /** Get all app register on apps.json
     * @return array Apps register
     */

    public static function registerBaseApps():array
    {
        $classes = self::getBaseClassFile();

        $apps = array();
        foreach ($classes as $class => $val) {
            $val = (array)$val;
            $apps[$val['name']] =  array("name" => $val['name'], "appclass" => new $val['class'](),
                "base_url" => $val['base_url'], "status_dev" => $val['status_dev'],
                "status_prod" => $val['status_prod']);
        };

        return $apps;
    }
}
