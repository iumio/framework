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
 * Class AppLocaleInterface
 * @package iumioFramework\Core\Base\Locale
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
interface AppLocaleInterface
{
    /**
     * Apply all modification frim AppLocale object
     * @return bool
     */
    public function apply():bool;
    
    public function isEnabled(): bool;

    /**
     * 
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled);

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @param array $values
     */
    public function setValues(array $values);
    /**
     * @return string|null
     */
    public function getTranstype(): ?string;

    /**
     * @param string|null $transtype
     */
    public function setTranstype(?string $transtype);
}
