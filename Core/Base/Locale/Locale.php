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

use iumioFramework\Core\Additional\EngineTemplate\SmartyEngineTemplate;
use iumioFramework\Core\Base\Http\HttpResponse;
use iumioFramework\Core\Base\Http\ParameterRequest;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Exception\Server\Server500;
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
     * @var string|null App name
     */
    private $app = null;

    /**
     * @var null|AppLocaleInterface Object of app locale
     */
    private $applocale = null;

    public function __construct()
    {
        $this->app = FEnv::get("app.call");
    }


    /**
     * Check if locale is enabled from framework instance
     * @return bool If is enabled or not
     */
    public function isEnabled():bool {
        $file = JsonListener::open(FEnv::get("framework.config.file"));
        if (true === property_exists($file, "locale_enabled") && true === $file->locale_enabled) {
            JsonListener::close(FEnv::get("framework.config.file"));
            return (true);
        }
        return (false);
    }


    /** Get local
     * @param string $appname
     * @return AppLocaleInterface
     * @throws Server500
     */
    public function getLocalesFrom():AppLocaleInterface {
        $appname = $this->app;
        if (true === JsonListener::exists(FEnv::get("app.config.file"))) {
            if (false === $this->isEnabled()) {
                throw new Server500(new \ArrayObject(
                    array("explain" => "Cannot get locale from $appname : Global locale mode is not enabled.",
                        "explain" => "Please enable the locale mode in framework.config.json to use it from your apps")
                ));
            }
            $file = JsonListener::open(FEnv::get("app.config.file"));
            if (true === property_exists($file, "locale_enabled") &&
                true === $file->locale_enabled &&
                true === property_exists($file, "trans_type") &&
                true === property_exists($file, "locale_values")) {
                        $vals = $file->locale_values;
                        $enabled = $file->locale_enabled;
                        $transtype = $file->trans_type;
                        $applocale = new AppLocale();
                        $applocale->setEnabled($enabled);
                        $applocale->setTranstype($transtype);
                        $applocale->setValues((array) $vals);
                        $this->applocale = $applocale;
                        return ($applocale);
            }
            throw new Server500(new \ArrayObject(
                array("explain" => "Cannot get locale from $appname : App locale mode is not enabled.",
                    "explain" => "Please enable the locale mode in config.json to use it from $appname")));
        }
        throw new Server500(new \ArrayObject(
            array("explain" => "Cannot get locale from $appname : App locale mode is not enabled.",
                "explain" => "Please enable the locale mode in config.json to use it from $appname")));
    }

    public function applyMatchLocale() {
        ini_set('intl.default_locale', 'de-DE');
        echo \Locale::getDefault();
        echo '; ';
        \Locale::setDefault('fr');
        echo \Locale::getDefault();
    }
}
