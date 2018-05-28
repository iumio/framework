<?php

/**
 **
 ** This is an iumio Framework component
 **
 ** (c) RAFINA DANY <dany.rafina@iumio.com>
 **
 ** iumio Framework, an iumio component [https://iumio.com]
 **
 ** To get more information about licence, please check the licence file
 **
 **/

namespace iumioFramework\Core\Routing;

use iumioFramework\Core\Base\Http\HttpListener;
use iumioFramework\Core\Base\Http\ServerRequest;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Base\Locale\AppLocale;
use iumioFramework\Core\Base\Locale\Locale;
use iumioFramework\Core\Base\Server\GlobalServer;
use iumioFramework\Core\Requirement\Environment\FrameworkEnvironment;
use iumioFramework\Core\Requirement\FrameworkCore;
use iumioFramework\Core\Requirement\FrameworkServices\AppTools;
use iumioFramework\Core\Requirement\FrameworkServices\FrameworkTools;
use iumioFramework\Core\Routing\Listener\MercureListener;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Requirement\Environment\FrameworkEnvironment as Env;
use iumioFramework\Core\Exception\Server\Server404;
use iumioFramework\Core\Exception\Server\Server405;
use iumioFramework\Core\Exception\Server\Server500;

/**
 * Class Routing
 * @package iumioFramework\Core\Routing
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class Routing extends MercureListener
{
    private $app;
    private $prefix;
    private $isbase;

    /**
     * Register a router to FrameworkCore
     */

    /**
     * Routing constructor.
     * @param string|null $app App name
     * @param string|null $prefix app prefix name
     * @param bool $isbase Check is a base app
     */
    public function __construct(string $app = null, string $prefix = null, bool $isbase = false)
    {
        $this->app =  $app;
        $this->prefix = $prefix;
        $this->isbase = $isbase;
        parent::__construct($app, $prefix);
    }

    /** Register a router
     * @return bool Is callable
     * @throws
     */
    public function routingRegister():bool
    {
        return ((parent::open($this->isbase) == 1)? true : false);
    }

    /** Get all route
     * @return array Route result
     */
    public function routes()
    {
        return ($this->router);
    }

    /** Remove blank data in array
     * @param array $routes Route array
     * @return array Array cleared
     */
    final public static function removeEmptyData(array $routes):array
    {
        $c = count($routes);
        for ($i = 0; $i < $c; $i++) {
            if (trim($routes[$i]) == "" || $routes[$i] == null || empty($routes[$i])) {
                unset($routes[$i]);
            }
        }
        return ($routes);
    }


    /** Check match of Method request and method request route
     * @param  mixed $ctr Controller parameters
     * @param string $met_call Method request called
     * @return int Is a success
     * @throws Server405
     */
    public static function checkRouteMatchesMethod($ctr, string $met_call):int
    {
        if (isset($ctr["m_allow"]) && in_array("ALL", $ctr["m_allow"])) {
            return (1);
        } elseif (isset($ctr["m_allow"]) && is_string($ctr["m_allow"]) && $ctr["m_allow"] === $met_call) {
            return (1);
        } elseif (isset($ctr["m_allow"]) && is_array($ctr["m_allow"]) && in_array($met_call, $ctr["m_allow"])) {
            return (1);
        } else {
            throw new Server405(new \ArrayObject(array("explain" =>
                "Method request $met_call is not allowed for this route", "solution" =>
                "Route required methods : ".json_encode($ctr["m_allow"]))));
        }
    }

    /** Compare the local on url
     * @param string $appname The appname
     * @param array $webroutes Webroute argurment (URL splited into array)
     * @return array|null The result
     * @throws
     */
    public static function localGetPart(string $appname, array $webroutes, string $stringwebroute):?array
    {
        if (true === Locale::isEnabled()) {
            $lapp = new AppLocale($appname);
            if (true === $lapp->isEnabled()) {
                foreach ($lapp->getValues() as $one) {
                    $comp = $webroutes[0] ?? null;
                    if (true === is_object($one)) {
                        foreach ((array)$one as $second) {
                            if ($comp === $second) {
                                array_unshift($webroutes, $second);
                                $webroutes = array_values(array_diff($webroutes, array($second)));
                                $stringwebroute = str_replace("/$second/", "/", $stringwebroute);
                                return (["locale" => $second, "object" => $lapp,
                                    "full_webroute" => $stringwebroute, "array_webroute" => $webroutes]);
                            }
                        }
                    } elseif ($one === $comp) {
                        $webroutes = array_values(array_diff($webroutes, array($comp)));
                        $stringwebroute = str_replace("/$comp/", "/", $stringwebroute);
                        return (["locale" => $one, "object" => $lapp,
                            "full_webroute" => $stringwebroute, "array_webroute" => $webroutes]);
                    }
                }
            }
        }
        return (null);
    }

    /** Get similarity routes
     * @param $appRoute string the app route
     * @param $webRoute string The URI
     * @param $route array Params app name
     * @param $proprietary string Route proprietary
     * @return array Similarity and match
     * @throws
     */
    public static function matches(string $appRoute, string $webRoute, array $route, string $proprietary = null):array
    {
        $paramValues = array();

        if ($pos = strpos($webRoute, "/?")) {
            return (array("is" => "nomatch", "similar" => 0));
        }
        if ($pos = strpos($webRoute, "?")) {
            $webRoute = substr_replace($webRoute, '', $pos, (strlen($webRoute) - 1));
        }

        $aRE = explode('/', $appRoute);
        $wRE = explode('/', $webRoute);

        $base = (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != "")? $_SERVER['SCRIPT_NAME'] : "";

        $script = "";


        if (strpos($webRoute, Env::getFileEnv(FEnv::get("framework.env"))) !== false) {
            $script = "/".Env::getFileEnv(FEnv::get("framework.env"));
            $key = array_search(Env::getFileEnv(FEnv::get("framework.env")), $wRE);
            unset($wRE[$key]);
            $wRE = array_values($wRE);
            $wRE = array_values(self::removeEmptyData($wRE));
            $webRoute = implode("/", $wRE);
            if (isset($webRoute[0]) && $webRoute[0] != "/") {
                $webRoute = "/".$webRoute;
            }
        }

        if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != "") {
            $remove = explode('/', $_SERVER['SCRIPT_NAME']);
            $remove = array_values(self::removeEmptyData($remove));
            $remove = array_values($remove);

            for ($z = 0; $z < count($remove); $z++) {
                $key = array_search($remove[$z], $wRE);
                if ($key !== false) {
                    unset($wRE[$key]);
                }
            }

            $wRE = array_values($wRE);
            $wRE = array_values(self::removeEmptyData($wRE));
        }

        if (strpos($base, Env::getFileEnv(FEnv::get("framework.env"))) !== false) {
            $rm = explode('/', $base);
            $rm = array_values(self::removeEmptyData($rm));
            $rm = array_values($rm);
            $key = array_search(Env::getFileEnv(FEnv::get("framework.env")), $rm);
            unset($rm[$key]);
            $rm = array_values($rm);
            $base = implode("/", $rm);
            if (isset($base[0]) && $base[0] != "/") {
                $base = "/".$base;
            }
        }


        if (trim($webRoute) === "") {
            $webRoute = "/";
        }

        $wRE = array_values(self::removeEmptyData(array_values($wRE)));
        $aRE = array_values(self::removeEmptyData(array_values($aRE)));

        $locale = self::localGetPart($route['app_proprietary'], $wRE, $webRoute);
        if (null !== $locale) {
            $webRoute = $locale["full_webroute"];
            $wRE = $locale["array_webroute"];
        }
        if (($base . $appRoute == $webRoute) || $base . $appRoute . "/" == $webRoute) {
            self::checkDefaultLocal($route['app_proprietary'], $base . $appRoute, $locale);
            return (array("is" => "same", "similar" => 100, "locale" => $locale));
        }

        if ((count($aRE) == count($wRE)) && count($aRE) > 0) {
            for ($i = 0; $i < count($aRE); $i++) {
                if ($aRE[$i] != $wRE[$i]) {
                    array_push($paramValues, $wRE[$i]);
                }
            }
        }

        if (isset($route['params'])) {
            if (count($aRE) == count($wRE)) {
                if (false === self::advancedMatch($aRE, $wRE)) {
                    return (array("is" => "nomatch", "similar" => 0));
                }
                similar_text($base.$script.$appRoute, $webRoute, $pe1);
                similar_text($base.$script.$appRoute."/", $webRoute, $pe2);

                $simi = self::checkArrayRoute($wRE, $aRE);
                $pe = ($pe1 > $pe2)? $pe1 : $pe2;

                if ($simi == 0) {
                    $pe = 0;
                }

                self::checkDefaultLocal($route['app_proprietary'], $base . $appRoute, $locale);
                return (array("is" => "partial", "result" => $paramValues, "similar" => $pe, "locale" => $locale));
            }
        }
        return (array("is" => "nomatch", "similar" => 0));
    }

    /** Check similarity of array (Route request vs App route)
     * @param array $web Array web route
     * @param array $app Array app route
     * @return int Similarity point
     */
    final private static function checkArrayRoute(array $web, array $app):int
    {
        $score = 0;
        $first = 0;
        for ($i = 0; $i < count($web); $i++) {
            if ($i == 0 && isset($web[0]) && isset($app[0]) && ($web[0] == $app[0])) {
                $first = 1;
            }
            if ($first > 0 && $web[$i] == $app[$i]) {
                $score++;
            }
            if ($first > 0 && strpos($app[$i], "{") != false &&
                strpos($app[$i], "}") != false && $web[$i] != "") {
                $score++;
            }
        }

        return ($score);
    }

    /**
     * @param array $a
     * @param array $b
     * @param string $name
     * @return array
     * @throws Server404
     * @throws Server500
     */
    final public function checkParametersTypeURI(array $a, array $b, string $name):array
    {
        $keys1 = array_keys($a);
        $keys2 = array();
        $values1 = array_values($a);
        $return = array();
        foreach ($b as $one) {
            $keysaf2[$one[0]] = $one[1];
        }
        $keys2 = array_map('trim', array_keys($keysaf2));

        if ($keys1 != $keys2) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Parameters type declaration does not matches with required parameters (".
                json_encode($keys2)." vs ".json_encode($keys1).")",
                "solution" => "Please check Mercure file")));
        }

        $values2 = array_map('trim', array_values($keysaf2));

        for ($i = 0; $i < count($values1); $i++) {
            $rs = $this->scalarTest(trim($values1[$i]), $values2[$i]);
            if (!$rs) {
                throw new Server404(new \ArrayObject(array("explain" =>
                    "Mecure parameter [".$keys1[$i]."] cannot be converted to ".$values2[$i]." for route [$name]",
                    "solution" => "Please check Mercure file")));
            }
            $return[$keys1[$i]] = $this->scalarConvert(trim($values1[$i]), $values2[$i]);
        }
        return ($return);
    }


    final public static function checkDefaultLocal(string $appname, string $route, $llc)
    {
        $locale = new AppLocale($appname);
        if (null === $llc && null !== $locale->getPrefer() && true == $locale->isEnabled()) {
            $values = $locale->getValues();
            if (isset($values[0])) {
                $values = $values[0];
                $redirect = $values->{$locale->getPrefer()};

                $reqs = FrameworkCore::getRuntimeParameters()->server;

                if (false === strpos($reqs->get("REQUEST_URI"), $reqs->get("SCRIPT_NAME"))) {
                    $url = "/$redirect$route";
                } else {
                    $clear = str_replace($reqs->get("SCRIPT_NAME"), "", $reqs->get("REQUEST_URI"));
                    $url = $reqs->get("SCRIPT_NAME")."/".$redirect.$clear;
                }

                $server = new GlobalServer();
                $https = ((null !== $server->get("HTTPS") &&
                    "on" === $server->get("HTTPS"))? "https://" : "http://");
                $query = ((!empty($reqs->get("QUERY_STRING")))? "?".$reqs->get("QUERY_STRING") : "");

                header("Status: 301 Moved Permanently", false, 301);
                header("Location: ".$https.$reqs->get("HTTP_HOST").$url.$query);

                exit();
            }
        }
    }


    /** Redirect to an app route
     * @param string $routename route name
     * @param array $params Parameter if route have any parameters
     * @param bool $domain If domain is included on url
     * @param array $query Query string
     * @throws Server500
     * @throws \Exception
     */
    final public static function redirectToRoute(
        string $routename,
        array $params = array(),
        bool $domain = true,
        array $query = array(),
        int $status = 302
    ):void {

        $querystring = $domainstring = "";
        $route = self::generateRoute($routename, $params);
        if (!empty($query)) {
            foreach ($query as $one => $value) {
                $querystring .= "$one=$value".(((end($query) !== $value))? "&": "");
            }
            $querystring = "?$querystring";
        }

        if (true === $domain) {
            $server = new GlobalServer();
            $https = ((null !== $server->get("HTTPS") &&
                "on" === $server->get("HTTPS"))? "https://" : "http://");
            $domain = $https.$server->get("HTTP_HOST");
        }
        header("Location: $domain$route$querystring", $status);
        exit(1);
    }



    /**
     * Redirect to a new url
     * @param string $url the url to redirect
     * @param array $query Query string on url
     */
    final public static function redirect(
        string $url,
        array $query = array(),
        bool $domain = true,
        int $status = 302
    ) {
        $newurl = $url;
        $iterator = 0;
        $domainstring = "";
        foreach ($query as $one => $value) {
            $newurl .= ((0 === $iterator)? "?" : "")."$one=$value".((count($query) === $iterator + 1)? "" : "&");
        }
        if (true === $domain) {
            $server = new GlobalServer();
            $https = ((null !== $server->get("HTTPS") &&
                "on" === $server->get("HTTPS"))? "https://" : "http://");
            $domainstring = $https.$server->get("HTTP_HOST");
        }

        if (isset($newurl[0]) && $newurl[0] !== "/") {
            $newurl = "/".$newurl;
        }
 
        header("Location: $domainstring$newurl", true, $status);
        exit(1);
    }


    /** Generate a route by name
     * @param string $routename route name
     * @param array|null $parameters route parameters
     * @param string|null $app_called App name
     * @param bool $component Is a component or not
     * @return string The route result
     * @throws Server500
     * @throws \Exception
     */
    final public static function generateRoute(
        string $routename,
        array $parameters = null,
        string $app_called = null,
        bool $component = false
    ) :string {
        $app = (($app_called != null)? $app_called : FEnv::get("app.call"));
        $file = JsonListener::open(FEnv::get("framework.config.core.apps.file"));
        $prefix = null;
        foreach ($file as $one) {
            if ($one->name == $app && $one->prefix != "") {
                $prefix = $one->prefix;
            }
        }
        JsonListener::close(FEnv::get("framework.config.core.apps.file"));
        $iscomponent = FrameworkTools::detectAppType($app);
        $component = false;
        if ($iscomponent == 'base') {
            $component = true;
        } elseif ($iscomponent == 'none') {
            if (FEnv::isset("framework.smarty.called") && FEnv::get("framework.smarty.called") == 1) {
                throw new \Exception("Cannot determine app type of ".$app);
            } else {
                throw new Server500(new \ArrayObject(array("explain" => "Cannot determine app type of " . $app,
                    "solution" => "Please check if your app exist")));
            }
        }
        $rt = new self($app, $prefix, $component);
        if (!$rt->routingRegister()) {
            if (FEnv::isset("framework.smarty.called") && FEnv::get("framework.smarty.called") == 1) {
                throw new \Exception("Cannot open your Mercure file");
            } else {
                throw new Server500(new \ArrayObject(array("solution" => "Please check all Mercure file",
                    "explain" => "Cannot open your Mercure file")));
            }
        }
        foreach ($rt->routes() as $one) {
            if ($one['routename'] == $routename) {
                $one['path'] = self::analysePath(
                    $one['routename'],
                    $one['path'],
                    ((is_array($parameters))? $parameters : array())
                );

                if (isset($one['path'][0]) && $one['path'][0] != "/") {
                    $one['path'] = "/".$one['path'];
                }
                $url = $one['path'];
                $base = (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] != "")? $_SERVER['SCRIPT_NAME'] : "";
                if (strpos(
                    $_SERVER['REQUEST_URI'],
                    FrameworkEnvironment::getFileEnv(FEnv::get("framework.env"))
                ) == false) {
                    $rm = explode('/', $base);
                    $rm = array_values(Routing::removeEmptyData($rm));
                    $rm = array_values($rm);
                    $key = array_search(FrameworkEnvironment::getFileEnv(FEnv::get("framework.env")), $rm);
                    unset($rm[$key]);
                    $rm = array_values($rm);
                    $base = implode("/", $rm);
                    if (isset($base[0]) && $base[0] != "/") {
                        $base = "/".$base;
                    }
                }
                $more = "";
                if (true === FEnv::isset("app.locale.context") &&
                    FEnv::get("app.call") === $one["app_proprietary"]) {
                    $more = "/".Locale::resolver(FEnv::get("app.locale.context"));
                }
                return ($more.$base.$url);
            }
        }
        if (FEnv::isset("framework.smarty.called") && FEnv::get("framework.smarty.called") == 1) {
            throw new \Exception("Unable to generate URL for route : $routename");
        } else {
            throw new Server500(new \ArrayObject(array("solution" => "Please check all Mercure file",
                "explain" => "Unable to generate URL for route : $routename")));
        }
    }


    /** Analyse path to change dynamic parameters with specific parameters array
     * @param string $routename The route name
     * @param string $path The route path
     * @param array $parameters Parameters to change
     * @return string The new path
     * @throws Server500 If parameters count does not match or parameter missing
     */
    final public static function analysePath(string $routename, string $path, array $parameters):string
    {
        $arraypath = explode("/", $path);
        $arrayElem = array();
        $narray = array();
        foreach ($arraypath as $one) {
            if (preg_match("/{(.*?)}/", $one)) {
                $nstr = str_replace("{", "", $one);
                $nstr = str_replace("}", "", $nstr);
                array_push($arrayElem, $nstr);
            }
        }

        $countchange = 0;
        if (count($parameters) != count($arrayElem)) {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Parameters count does not matches for $routename route",
                "solution" => "Please check your parameters declaration")));
        }



        foreach ($arrayElem as $uno) {
            if (isset($parameters[$uno]) && $parameters[$uno] != "") {
                if (gettype($parameters[$uno]) == "object" || gettype($parameters[$uno]) == "array") {
                    throw new Server500(new \ArrayObject(array("explain" => "Cannot generate route [".
                        $routename."] :  Invalid type [".gettype($parameters[$uno])."] for route parameters",
                        "solution" => "Define a valid parameter type ([object] and [array] is not allowed)")));
                }

                $narray["{".$uno."}"] = $parameters[$uno];
                $countchange++;
            }
        }

        if (count($parameters) != $countchange) {
            throw new Server500(new \ArrayObject(array("explain" => "Parameter(s) missing for $routename route",
                "solution" => "Please check your parameters declaration")));
        }


        for ($i = 0; $i < count($arraypath); $i++) {
            if (isset($narray[$arraypath[$i]])) {
                $arraypath[$i] = $narray[$arraypath[$i]];
            }
        }

        $path = implode("/", $arraypath);

        return ($path);
    }

    /** Advanced match for route with dynamic parameters
     * @param array $itemsapp Activity URL
     * @param array $itemsweb Request url
     * @return bool empty difference or not
     */
    private static function advancedMatch(array $itemsapp, array $itemsweb): bool
    {
        $max = count($itemsapp);
        for ($iterator = 0; $iterator < $max; $iterator++) {
            $last = strlen($itemsapp[$iterator]) - 1;
            if (isset($itemsapp[$iterator][0]) && "{" === $itemsapp[$iterator][0]
                && isset($itemsapp[$iterator][$last]) && "}" === $itemsapp[$iterator][$last]) {
                unset($itemsweb[$iterator]);
                unset($itemsapp[$iterator]);
            }
        }
        return (empty(array_diff($itemsapp, $itemsweb)));
    }
}
