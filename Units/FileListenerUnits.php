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

namespace iumioFramework\Units;

use iumioFramework\Core\Base\File\FileListener;
use iumioFramework\Core\Exception\Server\AbstractServer;

class FileListenerUnits extends FrameworkUnits
{
    /**
     * @throws \Exception
     * @throws \iumioFramework\Core\Exception\Server\Server500
     */
    public function putAssert()
    {
        $fl = new FileListener();
        $fl->open(ROOT_LOGS.'dev.log', 'a+');
        $fl->put("[HELLO]");
        $fl->close();
    }

    /**
     * @throws \Exception
     * @throws \iumioFramework\Core\Exception\Server\Server500
     */
    public function readAssert() {
        $f = new FileListener();
        $rs = $f->open(ROOT_LOGS.strtolower("dev").".log");
        $a =  array();
        print_r($f->read());
    }

    public function readByLineAssert() {
    }

    /**
     * @throws \Exception
     * @throws \iumioFramework\Core\Exception\Server\Server500
     */
    public function getLogs()
    {
        print_r(AbstractServer::getLogs());
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}