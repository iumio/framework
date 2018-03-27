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

namespace iumioFramework\Core\Requirement\Environment;

use iumioFramework\Core\Base\Http\HttpListener;
use iumioFramework\Core\Exception\Server\Server500;

/**
 * Class ProdEnvironment
 * iumio Class for production environment
 *
 * @package  iumioFramework\Core\Requirement
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class ProdEnvironment extends FrameworkEnvironment
{
    /** Start Application
     * @return int Is Ready
     * @throws Server500
     * @throws \Exception
     */
    public static function start():int
    {
        parent::definer('prod');
        if (self::hostAllowed() == 1) {
            self::enableComponents("prod");
            $core = new AppCore('prod', true);
            $request = HttpListener::createFromGlobals();
            $core->dispatching($request);
            return (1);
        }
        return (0);
    }
}