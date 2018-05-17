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

namespace ManagerApp\Masters;

use iumioFramework\Core\Masters\MasterCore;
use iumioFramework\Core\Base\Json\JsonListener as JL;
use iumioFramework\Core\Base\Renderer\Renderer;
use iumioFramework\Core\Requirement\Environment\FEnv;

/**
 * Class LocaleMaster
 * @package iumioFramework\Core\Manager
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class LocaleMaster extends MasterCore
{

    /**
     * Going to locale manager
     * @throws
     */
    public function localeActivity()
    {
        return ($this->render(
            "localemanager",
            array("selected" => "localemanager", "loader_msg" => "Locale Manager")
        ));
    }
}
