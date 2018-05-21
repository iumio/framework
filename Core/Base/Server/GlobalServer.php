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
use iumioFramework\Core\Exception\Server\Server500;

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
        parent::__construct((empty($server)) ? $_SERVER : $server);
    }

    /** Get info about current server
     * @param string $infoname info name
     * @return string info result
     * @throws Server500 Error generate
     */
    final public static function getServerInfo(string $infoname):string
    {
        $rs = 'none';
        switch ($infoname) {
            case 'PHP_VERSION':
                $rs = phpversion();
                break;
            default:
                try {
                    $rs = $_SERVER[$infoname];
                } catch (\Exception $e) {
                    throw new Server500(new \ArrayObject(array("explain" =>
                        "Core Error: The server info $infoname does not exist", "solution" => "Check your keyword")));
                }
                break;
        }
        return ($rs);
    }
}
