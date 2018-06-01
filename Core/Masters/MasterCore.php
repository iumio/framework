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

namespace iumioFramework\Core\Masters;

use iumioFramework\Core\Base\Renderer\Renderer;
use iumioFramework\Core\Base\Container\FrameworkContainer;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Routing\Routing;
use iumioFramework\Core\Additional\EngineTemplate\SmartyEngineTemplate;
use iumioFramework\Core\Requirement\FrameworkServices\Services;
use iumioFramework\Core\Requirement\FrameworkCore;
use iumioFramework\Core\Requirement\FrameworkServices\GlobalCoreService;
use iumioFramework\Core\Base\Database\DatabaseAccess as IDA;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Base\Http\Session\HttpSession;
use iumioFramework\Core\Base\Json\JsonListener as JL;

/**
 * Class MasterCore
 * This is the parent master for all subclass
 * @package iumioFramework\Core\Masters
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class MasterCore extends GlobalCoreService
{
    protected $masterFirst = null;
    protected $appMastering = null;
    protected $container;


    /**
     * MasterCore constructor.
     */
    public function __construct()
    {
        $this->container = FrameworkContainer::getInstance();
        self::setAppMaster($this);
    }

    /** Get a component
     * @param string $component
     * @return mixed
     * @throws Server500
     */
    final protected function get(string $component)
    {
        switch ($component) {
            case 'request':
                return (FrameworkCore::getRuntimeParameters())->request;
                break;
            case 'query':
                return (FrameworkCore::getRuntimeParameters())->query;
                break;
            case 'session':
                return (new HttpSession());
                break;
            case 'service':
                return (Services::getInstance());
                break;
            case 'locale':
                return (FEnv::get("app.locale.context"));
                break;
            default:
                throw new Server500(new \ArrayObject(array("explain" =>
                   "Cannot call component : Undefined component $component",
                   "solution" => "Call availables components")));
                break;
        }
    }



    /** Show a view
     * @param string $view View name
     * @param array $options options to view
     * @param bool $iscached By default to true, this option allows you to disable
     * or enable the cache for a page (Useful for dynamic content of a page)
     * @throws * if class does not exist
     * @return Renderer
     */
    final protected function render(string $view, array $options = array(), bool $iscached = true):Renderer
    {
        $r = new Renderer();
        return ($r->graphicRenderer($view, $options, $iscached));
    }

    /** Change views Render extension
     * @param string $ext new extention
     * @return bool
     */
    final protected function changeRenderExtention(string $ext):bool
    {
        SmartyEngineTemplate::$viewExtention = $ext;
        return (true);
    }

    /** Register a new view plugin
     * This plugin allow to use in your smarty view
     * @param string $type Method type (function or modifier)
     * @param string $name Method name
     * @param array $method This array contain class with namespace and class method
     * array('Class with namespace', 'class method')
     * @return int Return the register success
     * @throws Server500
     * @throws \SmartyException
     */
    final public function registerViewPlugin(string $type, string $name, array $method):int
    {
        $si = SmartyEngineTemplate::getSmartyInstance($this->appMastering);
        if ($type !== "modifier" && $type != "function") {
            throw new Server500(new \ArrayObject(array("explain" => "Undefined plugin type $type.",
                "solution" => "Allowed to modifier or function")));
        }
        if (is_array($method) && count($method) == 2) {
            $si->registerPlugin($type, $name, $method);
        } else {
            throw new Server500(new \ArrayObject(array("explain" => "You must enter a valid class method in this array",
                "solution" => "array('Class with namespace', 'class method')")));
        }
        return (1);
    }

    /** Get Database service
     * @param string|null $name Database name
     * @return \PDO PDO instance (Connection instance)
     */
    final protected function getConnection(string $name = '#none'):\PDO
    {
        return (IDA::getDbInstance($name));
    }


    /** Generate route url
     * @param string $routename route name in routing file
     * @param array $parameters Parameters for dynamic parameters in url
     * @param string $app_called App name
     * @param bool $component Is a application component
     * @return string|NULL The generated route
     * @throws Server500
     * @throws \Exception
     */

    final public function generateRoute(
        string $routename,
        array $parameters = null,
        string $app_called = null,
        bool $component = false
    ) :string {
        return (Routing::generateRoute($routename, $parameters, $app_called, $component));
    }



    /** Return instance of specific master in current app
     * @param string $mastername master name
     * @return mixed Class instance
     * @throws Server500
     */
    final protected function getMaster(string $mastername)
    {
        if (FEnv::get("app.is_components") == 1) {
            $file = JL::open(FEnv::get("framework.baseapps.apps.file"));
        } else {
            $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        }

        $app = null;

        foreach ($file as $one => $val) {
            if (isset($val->name) && FEnv::get("app.call") == $val->name) {
                $app = $val;
                break;
            }
        }

        if ($app != null) {
            $class = FEnv::get("app.call")."\Masters\\".$mastername."Master";
            if (class_exists($class)) {
                return (new $class);
            } else {
                throw new Server500(new \ArrayObject(array("explain" => "Master $mastername does not exist",
                    "solution" => "Please check masters declaration")));
            }
        } else {
            throw new Server500(new \ArrayObject(array("explain" =>
                ((FEnv::get("app.is_components") == 1)? "BaseApp" : "App" )." ".
                FEnv::get("app.call")." does not exist in apps.json",
                "solution" => "Please check app declaration")));
        }
    }
}
