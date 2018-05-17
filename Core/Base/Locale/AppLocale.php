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
    private $enabled;

    /**
     * @var $values array
     */
    private $values;

    /**
     * @var $transtype string|null
     */
    private $transtype;


    /**
     * AppLocale constructor.
     * @param string|null $appname appname
     */
    public function __construct(?string $appname = null)
    {
        if (null !== $appname) {
            $file = JsonListener::open(FEnv::get("app.config.file"));
            $this->enabled = (property_exists($file, "locale_enabled"))? $file->locale_enabled : false;
            $this->values = (property_exists($file, "locale_values"))? (array)$file->locale_values : [];
            $this->transtype = (property_exists($file, "trans_type"))? $file->trans_type : null;
        }
    }

    public function apply(): bool
    {
        $file = JsonListener::open(FEnv::get("app.config.file"));
        $file->locale_enabled = $this->enabled;
        $file->locale_values = $this->values;
        $file->trans_type = $this->transtype;
        return (JsonListener::put("app.config.file", json_encode($file)));
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
    
    


    
}
