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

namespace iumioFramework\Core\Base\Server;

use iumioFramework\Core\Base\Http\ParameterRequest;

/**
 * Class GlobalServer
 * @package iumioFramework\Core\Base|Server
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class GlobalServer extends ParameterRequest
{
    public function __construct(array $server = [])
    {
        parent::__construct((empty($server))? $_SERVER : $server);
    }
}
