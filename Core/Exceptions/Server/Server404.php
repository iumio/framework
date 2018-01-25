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

namespace iumioFramework\Exception\Server;

use ArrayObject;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Requirement\Environment\FEnv;

/**
 * Class Server404
 * @package iumioFramework\Exception\Server
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class Server404 extends AbstractServer
{
    /**
     * Server404 constructor.
     * @param ArrayObject $component
     * @param $none string parameter not used. it's for the interface
     */
    public function __construct(ArrayObject $component, string $none = null)
    {
        $this->code = '404';
        $this->codeTitle = 'NOT FOUND';
        $this->explain =  'The resource you try to access is not found.';
        $this->solution = null;
        $this->env = FEnv::get("framework.env");
        $e = JsonListener::open(FEnv::get("framework.config.core.config.file"));
        if (empty($e)) {
            throw new Server500(new ArrayObject(
                array("explain" => "Framework Config file is empty : cannot generate this error [".$this->code."]",
                    "solution" => "Set a valid Framework Config file")));
        }
        $w = true;
        if (isset($e->{"404_log"}) && is_bool($e->{"404_log"}) && $e->{"404_log"} == false) {
            $this->inlog = false;
        }
        parent::__construct($component, 'Not found');
    }
}
