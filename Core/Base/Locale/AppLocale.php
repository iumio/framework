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
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\FrameworkServices\AppConfig;

/**
 * Class AppLocale
 * @package iumioFramework\Core\Base\Locale
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class AppLocale implements AppLocaleInterface
{
    /**
     * @var $enabled bool
     */
    private $enabled = false;

    /**
     * @var $values array
     */
    private $values;

    /**
     * @var $transtype string|null
     */
    private $transtype;

    /**
     * @var $prefer string|null
     */
    private $prefer;

    /**
     * @var null|string $appname the app name
     */
    private $appname = null;


    /**
     * AppLocale constructor.
     * @param string|null $appname appname
     * @throws
     */
    public function __construct(?string $appname = null)
    {
        if (null !== $appname) {
            $this->appname = $appname;
            if (true ===
               JsonListener::exists(FEnv::get("framework.root")."apps/$appname/config.json")) {
                $file = JsonListener::open(FEnv::get("framework.root") . "apps/$appname/config.json");
                $this->enabled = (property_exists(
                    $file,
                    "locale_enabled"
                )) ? (bool)$file->locale_enabled : false;
                $this->values = (property_exists($file, "locale_values")) ? (array)$file->locale_values : [];
                $this->transtype = (property_exists($file, "trans_type")) ? $file->trans_type : null;
                $this->prefer = (property_exists($file, "prefer_locale")) ? $file->prefer_locale : null;
            }
        }
    }

    /** Apply object persist
     * @return bool
     * @throws Server500
     */
    public function apply(): bool
    {
        $path = FEnv::get("framework.root")."apps/".$this->appname."/config.json";
        if (false === JsonListener::exists($path)) {
            AppConfig::createHimSelf($this->appname);
        }
       
        $file = JsonListener::open($path);
        $file->locale_enabled = $this->enabled;
        $file->locale_values = $this->values;
        $file->trans_type = $this->transtype;
        $file->prefer_locale = $this->prefer;
        return (JsonListener::put($path, json_encode($file, JSON_PRETTY_PRINT)));
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getTranstype(): ?string
    {
        return $this->transtype;
    }

    /**
     * @param string $transtype
     */
    public function setTranstype(?string $transtype)
    {
        $this->transtype = $transtype;
    }

    /**
     * @return null|string
     */
    public function getAppname(): ?string
    {
        return $this->appname;
    }

    /**
     * @param null|string $appname
     */
    public function setAppname(?string $appname): void
    {
        $this->appname = $appname;
    }

    /**
     * @return string|null
     */
    public function getPrefer(): ?string
    {
        return $this->prefer;
    }

    /**
     * @param string|null $prefer
     */
    public function setPrefer(?string $prefer): void
    {
        $this->prefer = $prefer;
    }
}
