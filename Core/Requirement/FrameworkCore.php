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

namespace iumioFramework\Core\Requirement;

use iumioFramework\Core\Base\Locale\Locale;
use iumioFramework\Core\Base\Renderer\Renderer;
use iumioFramework\Core\Requirement\FrameworkServices\AppConfig;
use iumioFramework\Core\Requirement\FrameworkServices\AppTools;
use iumioFramework\Core\Requirement\FrameworkServices\FrameworkTools;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Base\Http\HttpListener;
use iumioFramework\Core\Exception\Access\Access200;
use iumioFramework\Core\Routing\Routing;
use iumioFramework\Core\Requirement\Reflection\FrameworkReflection;
use iumioFramework\Core\Requirement\FrameworkServices\GlobalCoreService;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Exception\Server\Server404;

/**
 * Class FrameworkCore
 * The Core is the heart of the iumio system.
 * It manages an environment made of app.
 * @package iumioFramework\Core\Requirement;
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

abstract class FrameworkCore extends GlobalCoreService
{
    protected $apps = array();
    protected $debug;
    protected $environment;
    private static $runtime_parameters = null;

    public const CORE_VERSION = '0.9.0';
    public const CORE_NAME = 'SUN';
    public const CORE_STAGE = 'RC';
    public const CORE_BUILD = 201790;
    protected static $edition = array();

    /**
     * Constructor.
     *
     * @param string $environment The app environment
     * @param bool   $debug       Enable debug
     * @throws Server500
     */

    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = (bool) $debug;
        FrameworkTools::detectFirstInstallation();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }
        FrameworkTools::declareExceptionHandlers();
        self::setCore($this);
        $defClass = new \ReflectionMethod($this, '__construct');

        $defClass = $defClass->getDeclaringClass()->name;
    }

    /** Set debug mode
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /** Get runtime parameters request
     * @return null|array
     */
    final public static function getRuntimeParameters()
    {
        return self::$runtime_parameters;
    }

    /** Get application environment
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }



    /** Go to controller
     * @param HttpListener $request parameters
     * @return int Return as success
     * @throws Server404 Url not found
     * @throws Server500|\Exception Class or method does not exist or router failed
     */
    public function dispatching(HttpListener $request):int
    {
        self::$runtime_parameters = $request;
        $apps = AppTools::registerApps();
        $bapps = AppTools::registerBaseApps();
        $great = false;

        if ($this->isComponentCall($bapps, $request)) {
            return (1);
        }
        
        $values = AppTools::getSimpleAppFormat($apps);
        foreach ($values as $one => $def) {
            if ($great) {
                return (1);
            }
            if ($def["value"]["enabled"] == "no") {
                continue;
            }
            $rt = new Routing($def['name'], $def['value']['prefix']);
            if ($rt->routingRegister() == true) {
                $callback = FrameworkTools::manage($request, $rt->routes());
                if ($callback != null) {
                    Routing::checkRouteMatchesMethod($callback, strtoupper($request->getMethod()));
                    $method = $callback['method'];
                    $controller = $callback['controller'];
                    $defname = $def['name'];
                    $master = "\\$defname\\Masters\\{$controller}Master";
                    $call = new FrameworkReflection();
                    FEnv::set("app.call", $def['name']);
                    FEnv::set("app.is_components", false);
                    Locale::applyLocale($callback["locale"] ?? null);
                    if (isset($callback['pval'])) {
                        if (isset($callback['r_parameters']) && count($callback['r_parameters']) > 0) {
                            $callback['pval'] = $rt->checkParametersTypeURI(
                                $callback['pval'],
                                $callback['r_parameters'],
                                $callback["routename"]
                            );
                        }
                        $rscall = $call->__named($master, $method, $callback['pval']);
                    } else {
                        $rscall =  $call->__named($master, $method);
                    }
                    if (!($rscall instanceof Renderer)) {
                        $ac_called = FEnv::get("app.activity_called");
                        throw new Server500(new \ArrayObject(
                            array("explain" => "The activity {".$ac_called['method'].
                                "} result in object {".$ac_called['class'].
                                "} must be a Renderer : ".ucfirst(gettype($rscall))." is given", "solution" =>
                            "Return a Renderer instance in this activity")
                        ));
                    }
                    $great = true;
                    new Access200();
                    $rscall->pushRender();
                }
            } else {
                throw new Server500(new \ArrayObject(array("explain" => "Router register failed  ", "solution" =>
                    "Please check your app configuration")));
            }
        }
        if ($great == false) {
            throw new Server404(new \ArrayObject(array("solution" => "Please check your URI")));
        }

        return (1);
    }

    /** Check if it's component is calling
     * @param array $bases Base Apps
     * @param HttpListener $request Current request
     * @return bool If component is calling
     * @throws Server500 Generate error server
     * @throws \Exception
     */
    public function isComponentCall(array $bases, HttpListener $request): bool
    {
        foreach ($bases as $def) {
            if (((FEnv::get("framework.env") == "dev") && ($def['status_dev'] == "off")) ||
                ((FEnv::get("framework.env") == "prod") && ($def['status_prod'] == "off"))) {
                continue;
            }
            if (isset($def['appclass'])) {
                if (method_exists($def['appclass'], 'off') == true) {
                    $rt = new Routing($def['name'], null, true);
                    if ($rt->routingRegister() == true) {
                        $callback = FrameworkTools::manage($request, $rt->routes());
                        if ($callback != null) {
                            Routing::checkRouteMatchesMethod($callback, strtoupper($request->getMethod()));
                            (AppConfig::getInstance($def['name']))->checkRequirements();
                            $method = $callback['method'];
                            $controller = $callback['controller'];
                            $defname = $def['name'];
                            $master = "\\$defname\\Masters\\{$controller}Master";
                            try {
                                $call = new FrameworkReflection();
                                FEnv::set("app.call", $def['name']);
                                FEnv::set("app.is_components", true);
                                if (isset($callback['pval'])) {
                                    if (count($callback['r_parameters']) > 0) {
                                        $callback['pval'] = $rt->checkParametersTypeURI(
                                            $callback['pval'],
                                            $callback['r_parameters'],
                                            $callback['routename']
                                        );
                                    }
                                    $rscall = $call->__named($master, $method, $callback['pval']);
                                } else {
                                    $rscall = $call->__named($master, $method);
                                }

                                if (!($rscall instanceof Renderer)) {
                                    $ac_called = FEnv::get("app.activity_called");
                                    throw new Server500(new \ArrayObject(array("explain" =>
                                        "The activity {".$ac_called['method'].
                                        "} result in object {".$ac_called['class'].
                                        "} must be a Renderer : ".ucfirst(gettype($rscall))." is given", "solution" =>
                                        "Return a Renderer instance in this activity")));
                                }
                                $rscall->pushRender();
                                return (true);
                            } catch (\Exception $exception) {
                                throw new Server500(new \ArrayObject(array("explain" => $exception->getMessage())));
                            }
                        }
                    } else {
                        throw new Server500(new \ArrayObject(array("explain" => $def['name'] .
                            " component not contain a related router", "solution" =>
                            "Please check the if 'routingRegister' is present in your router")));
                    }
                } else {
                    throw new Server500(new \ArrayObject(array("explain" => $def['name'] .
                        " component doesn't contain 'off' method ", "solution" =>
                        "Please add off method to your component")));
                }
            } else {
                throw new Server500(new \ArrayObject(array("explain" => "Component doesn't exist ", "solution" =>
                    "Check apps.json file in base app")));
            }
        }
        return (false);
    }


    /** Get info about iumio framework
     * @param string $infoname info name
     * @return string info result
     * @throws Server500 Error generate
     */
    final public static function getInfo(string $infoname):string
    {
        $rs = 'none';
        $edition = FrameworkTools::getEditionInfo();
        switch ($infoname) {
            case 'CORE_VERSION':
                $rs = self::CORE_VERSION;
                break;
            case 'CORE_BUILD':
                $rs = self::CORE_BUILD;
                break;
            case 'CORE_STAGE':
                $rs = self::CORE_STAGE;
                break;
            case 'CORE_NAME':
                $rs = self::CORE_NAME;
                break;
            case 'EDITION_BUILD':
                $rs = $edition->edition_build;
                break;
            case 'EDITION_VERSION':
                $rs = $edition->edition_version;
                break;
            case 'EDITION_STAGE':
                $rs = $edition->edition_stage;
                break;
            case 'EDITION_SHORTNAME':
                $rs = $edition->edition_shortname;
                break;
            case 'EDITION_FULLNAME':
                $rs = $edition->edition_fullname;
                break;
            case 'EDITION_U3I':
                $rs = $edition->u3i;
                break;
            case 'LOCATION':
                $rs =  realpath(__DIR__.DIRECTORY_SEPARATOR.'../../../../../');
                break;
        }
        return ($rs);
    }
}
