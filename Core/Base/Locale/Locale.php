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

namespace iumioFramework\Core\Base\Locale;

use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Requirement\Patterns\Singleton\SingletonClassicPattern;

/**
 * Class Locale
 * @package iumioFramework\Core\Base\Locale
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class Locale extends SingletonClassicPattern
{
    /**
     * @var array Teranslation file type
     */
    private $transType = ["poedit", "json"];

    /**
     * @var string|null App name
     */
    private $app = null;

    /**
     * @var null|AppLocaleInterface Object of app locale
     */
    private $applocale = null;

    /**
     * Locale constructor.
     * @throws Server500
     */
    public function __construct()
    {
        $this->app = FEnv::get("app.call");
        $this->getLocalesFrom();
    }


    /**
     * Check if locale is enabled from framework instance
     * @return bool If is enabled or not
     * @throws Server500
     */
    public static function isEnabled():bool
    {
        $file = JsonListener::open(FEnv::get("framework.config.core.config.file"));
        if (true === property_exists($file, "locale_enabled") && true === $file->locale_enabled) {
            JsonListener::close(FEnv::get("framework.config.core.config.file"));
            return (true);
        }
        return (false);
    }


    /**
     * Disable locale for framework instance
     * @return bool
     * @throws Server500
     */
    public static function disableLocale():bool
    {
        $file = JsonListener::open(FEnv::get("framework.config.core.config.file"));
        if (true === property_exists($file, "locale_enabled") && true === $file->locale_enabled) {
            $file->locale_enabled = false;
            JsonListener::put(
                FEnv::get("framework.config.core.config.file"),
                json_encode($file, JSON_PRETTY_PRINT)
            );
            JsonListener::close(FEnv::get("framework.config.core.config.file"));
            return (true);
        }
        return (false);
    }

    /**
     * Enable locale for framework instance
     * @return bool
     * @throws Server500
     */
    public static function enableLocale():bool
    {
        $file = JsonListener::open(FEnv::get("framework.config.core.config.file"));
        if (true === property_exists($file, "locale_enabled") && false === $file->locale_enabled) {
            $file->locale_enabled = true;
            JsonListener::put(
                FEnv::get("framework.config.core.config.file"),
                json_encode($file, JSON_PRETTY_PRINT)
            );
            JsonListener::close(FEnv::get("framework.config.core.config.file"));
            return (true);
        }
        return (false);
    }


    /** Get all local
     * @return array lacales array object
     * @throws Server500
     */
    public static function getAll():array
    {
        $arr = [];
        if (false === self::isEnabled()) {
            throw new Server500(new \ArrayObject(
                array("explain" => "Cannot get locale from  : Global locale mode is not enabled.",
                    "solution" => "Please enable the locale mode in framework.config.json to use it from your apps")
            ));
        }
        $fileapp = JsonListener::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($fileapp as $one) {
            if (true === JsonListener::exists(FEnv::get("framework.root")."apps/".
                    $one->{"name"}."/config.json")) {
                $file = JsonListener::open(FEnv::get("framework.root")."apps/".
                    $one->{"name"}."/config.json");
                if (true === property_exists($file, "locale_enabled") &&
                     true === $file->locale_enabled &&
                     true === property_exists($file, "trans_type") &&
                     true === property_exists($file, "locale_values") &&
                     true === property_exists($file, "prefer_locale")) {
                    $vals = $file->locale_values;
                    $enabled = $file->locale_enabled;
                    $transtype = $file->trans_type;
                    $preferlocale = $file->prefer_locale;
                    $applocale = new AppLocale();
                    $applocale->setEnabled($enabled);
                    $applocale->setTranstype($transtype);
                    $applocale->setValues((array) $vals);
                    $applocale->setAppname($one->{"name"});
                    $applocale->setPrefer($preferlocale);
                    $arr[$one->{"name"}] = $applocale;
                } else {
                    $arr[$one->{"name"}] = null;
                }
            } else {
                $arr[$one->{"name"}] = null;
            }
        }
        
        return ($arr);
    }

    /** Get local
     * @return AppLocaleInterface
     * @throws Server500
     */
    public function getLocalesFrom():AppLocaleInterface
    {
        $appname = $this->app;
        if (true === JsonListener::exists(FEnv::get("app.config.file"))) {
            if (false === self::isEnabled()) {
                throw new Server500(new \ArrayObject(
                    array("explain" => "Cannot get locale from $appname : Global locale mode is not enabled.",
                        "solution" => "Please enable the locale mode in framework.config.json to use it from your apps")
                ));
            }
            $file = JsonListener::open(FEnv::get("app.config.file"));
            if (true === property_exists($file, "locale_enabled") &&
                true === $file->locale_enabled &&
                true === property_exists($file, "trans_type") &&
                true === property_exists($file, "locale_values") &&
                true === property_exists($file, "prefer_locale")) {
                $vals = $file->locale_values;
                $enabled = $file->locale_enabled;
                $transtype = $file->trans_type;
                $preferlocale = $file->prefer_locale;
                $applocale = new AppLocale();
                $applocale->setEnabled($enabled);
                $applocale->setTranstype($transtype);
                $applocale->setValues((array) $vals);
                $applocale->setAppname($appname);
                $applocale->setPrefer($preferlocale);
                $this->applocale = $applocale;
                return ($applocale);
            }
            throw new Server500(new \ArrayObject(
                array("explain" => "Cannot get locale from $appname : App locale mode is not enabled.",
                "solution" => "Please enable the locale mode in config.json to use it from $appname")
            ));
        }
        throw new Server500(new \ArrayObject(
            array("explain" => "Cannot get locale from $appname : App locale mode is not enabled.",
            "solution" => "Please enable the locale mode in config.json to use it from $appname")
        ));
    }

    /**
     * Apply a locale
     * @param string $indexlocale Local match
     * @throws Server500
     */
    public function applyMatchLocale(string $indexlocale):void
    {
        $locale = $this->applocale;
        $values = ((array)$locale->getValues());
        $key = array_search($indexlocale, (array)$values[0] ?? []);
        if (null === $key || false === $key) {
            throw new Server500(new \ArrayObject(array("explain" => "Undefined locale value [$indexlocale]",
                "solution" => "Please refer to the config.json to set the correct local value")));
        }
        putenv("LC_ALL=$key");
        setlocale(LC_ALL, $key);
        FEnv::set("app.locale.context", $key);
        if (in_array($locale->getTranstype(), $this->transType)) {
            switch ($locale->getTranstype()) {
                case 'poedit':
                    bindtextdomain($locale->getAppname(), FEnv::get("framework.root") . "apps/" .
                        $locale->getAppname() . "/locales");
                    textdomain($locale->getAppname());
                    FEnv::set("app.locale.content.file", FEnv::get("framework.root") . "apps/" .
                        $locale->getAppname() . "/locales/$key/LC_MESSAGES/" . $locale->getAppname() . ".mo");

                    // Translation is looking for in locale/{localevalue}/LC_MESSAGES/{appname}.mo now
                    break;
                case 'json':
                    FEnv::set("app.locale.content.file", FEnv::get("framework.root") . "apps/" .
                        $locale->getAppname() . "/locales/" . strtolower($key) . ".json");
                    break;
            }
        }
    }

    /**
     * @param array|null $object
     */
    public static function applyLocale(?array $object)
    {
        if (null !== $object) {
            $applocale = $object["object"];
            $localtargeted = $object["locale"];
            if (true === $applocale->isEnabled()) {
                $la = Locale::getInstance();
                $la->applyMatchLocale($localtargeted);
            }
        }
    }

    /** Resolve the correct locale url from locale key
     * @param string $locale Locale string
     * @return string the locale url
     * @throws Server500
     */
    public static function resolver(string $locale):string
    {
        $file = JsonListener::open(FEnv::get("app.config.file"));
        if (true === property_exists($file, "locale_enabled") &&
            true === $file->locale_enabled) {
            $vals = $file->locale_values;
            return ($vals[0]->$locale);
        }
        throw new Server500(new \ArrayObject(
            array("explain" => "Cannot get locale : App locale mode is not enabled.",
            "solution" => "Please enable the locale mode in config.json to use it")
        ));
    }


    /** Translate a word
     * @param string $value Value to translate
     * @param string $lang lang value
     * @return null|string The translation
     * @throws Server500
     */
    public function trans(string $value, string $lang = null):?string
    {
        $locale = $this->applocale;
        if (null !== $lang) {
            $this->applyMatchLocale($lang);
        }

        if (in_array($locale->getTranstype(), $this->transType)) {
            switch ($locale->getTranstype()) {
                case 'poedit':
                    return (_($value));
                   break;
                case 'json':
                    $file = JsonListener::open(FEnv::get("app.locale.content.file"));
                    return ($file->$value);
                    break;
            }
        }
        return (null);
    }
}
