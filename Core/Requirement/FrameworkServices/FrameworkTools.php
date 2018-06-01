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

use iumioFramework\Core\Server\Server;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Base\Http\HttpListener;
use iumioFramework\Core\Routing\Routing;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Base\Json\JsonListener as JL;

/**
 * Class FrameworkTools
 * Tools for framework core
 * @package iumioFramework\Core\Requirement;
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class FrameworkTools extends GlobalCoreService
{
    protected $apps = array();
    protected static $edition = array();

    /** Detect the app type
     * @param string $appname App name
     * @return string The type of app called. Possibility to return a << none >> app when appname not detected
     * @throws
     */
    final public static function detectAppType(string $appname):string
    {
        $apptype = 'none';
        $appsp = AppTools::registerApps();
        $appbs = AppTools::registerBaseApps();

        foreach ($appsp as $one => $val) {
            if ($one == $appname) {
                return ('simple');
            }
        }

        foreach ($appbs as $one => $val) {
            if ($val['name'] == $appname) {
                return ('base');
            }
        }

        return ($apptype);
    }
   
    
    /**
     * Check the correct permission in directory :
     * /elements
     * /apps
     * @return int Correct permissions or not
     * @throws Server500 Permissions are incorrect
     */
    public static function checkPermission():int
    {
        if (!Server::checkIsExecutable(FEnv::get("framework.root")."elements/") ||
            !Server::checkIsReadable(FEnv::get("framework.root")."elements/") ||
            !Server::checkIsWritable(FEnv::get("framework.root")."elements/")) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Core Error : Folder /elements does not have correct permission",
                "solution" => "Must be read, write, executable permission")));
        }

        if (!Server::checkIsExecutable(FEnv::get("framework.root")."apps/") ||
            !Server::checkIsReadable(FEnv::get("framework.root")."apps/") ||
            !Server::checkIsWritable(FEnv::get("framework.root")."apps/")) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Core Error : Folder /apps does not have correct permission",
                "solution" => "Must be read, write, executable permission")));
        }
        return (1);
    }

    
    /** Get edition info linked with Framework Core
     * @return \stdClass edition infos
     * @throws Server500
     */
    final public static function getEditionInfo():\stdClass
    {
        $file = JL::open(FEnv::get("framework.config.core.config.file"));
        JL::close(FEnv::get("framework.config.core.config.file"));
        self::$edition = $file;
        return ($file);
    }

    /** Detect if it is a first install
     * @return int The success or failure
     * @throws Server500 File installer.php not exists
     */
    final public static function detectFirstInstallation():int
    {
        $file = JL::open(FEnv::get("framework.config.core.config.file"));
        if (!isset($file->installation) || ($file->installation == null)) {
            if (file_exists(FEnv::get("framework.root").'public/setup/setup.php')) {
                header('Location: '.FEnv::get("host.current").'/setup/setup.php');
                exit(1);
            } else {
                throw new \RuntimeException("(Setup components does not exist in web directory => Please download".
                    "the setup components on iumio Framework Website to fix this error and put him in web directory)");
            }
        }
        return (0);
    }

    /**
     * Declare the new method dedicated to exception
     */
    final public static function declareExceptionHandlers()
    {
        set_error_handler(
            'iumioFramework\Core\Exception\Tools\ToolsExceptions::errorHandler',
            E_ALL
        );

        set_exception_handler('iumioFramework\Core\Exception\Tools\ToolsExceptions::exceptionHandler');
        register_shutdown_function('iumioFramework\Core\Exception\Tools\ToolsExceptions::shutdownFunctionHandler');
    }



    /**
     * Detect url matches
     * @param HttpListener $request
     * @param array $routes
     * @param string $baseurl Contain base url if it's a component is calling
     * @return mixed
     */
    public static function manage(HttpListener $request, array $routes, string $baseurl = "")
    {
        $controller = null;
        $baseSimilar = 0;
        $path = $request->server->get('REQUEST_URI');
        
        if ($path == "") {
            $path = "/";
        }
        
        foreach ($routes as $route) {
            if ($route['visibility'] === "disabled") {
                continue;
            }
            $mat = Routing::matches($baseurl.$route['path'], $path, $route);
            if (($mat['similar'] > $baseSimilar)) {
                $baseSimilar = $mat['similar'];
                if (isset($mat["locale"])) {
                    $route["locale"] = $mat["locale"];
                }
                $controller = $route;

                if (isset($controller['params']) && count($controller['params']) > 0) {
                    $pval = AppTools::assembly($controller['params'], $mat['result']);

                    if ($pval != false) {
                        $controller['pval'] = $pval;
                        unset($controller['params']);
                    }
                }
            }
        }
        return ($controller);
    }
}
