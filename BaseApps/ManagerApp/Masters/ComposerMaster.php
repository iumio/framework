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
 * Class ComposerMaster
 * @package iumioFramework\Core\Manager
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class ComposerMaster extends MasterCore
{

    /**
     * Going to composer manager
     * @throws
     */
    public function composerActivity()
    {
        return ($this->render(
            "composermanager",
            array("selected" => "composermanager", "loader_msg" => "Compodrt Manager")
        ));
    }

    /**
     * Get all SmartyConfig
     * @throws
     */
    public function getAllActivity()
    {
        $file = JL::open(FEnv::get("framework.root")."composer.lock");
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK", "results" => $file)));
    }
}
