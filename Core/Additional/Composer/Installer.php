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

namespace iumioFramework\Composer;
@include_once __DIR__.'/../../ServerManager/ServerManager.php';

use iumioFramework\Core\Additionnal\Server\ServerManager as iSM;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

/**
 * Class Installer
 * @package iumioFramework\Composer
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class Installer
{
    static public $base_dir = __DIR__.'/../../../../../../';

    static public $base_dir_new = __DIR__.'/../../../../../';


    /**
     * @param Event $event
     * @throws \Exception
     */
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        self::do();
    }

    /**
     * @param Event $event
     * @throws \Exception
     */
    public static function postPackageInstall(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        self::do();
    }


    /**
     * Move some components downloaded by composer to the correct location
     * @throws \Exception
     */
    final public static function moveComponentsDownloadedByComposer() {
        iSM::move(self::$base_dir."vendor/twbs/bootstrap/dist/",
            self::$base_dir."public/components/libs/bootstrap");
        iSM::move(self::$base_dir."vendor/components/font-awesome/",
            self::$base_dir."public/components/libs/font-awesome/");
        iSM::move(self::$base_dir."vendor/components/jquery/",
            self::$base_dir."public/components/libs/jquery/");
        iSM::move(self::$base_dir."vendor/daneden/animate.css/",
            self::$base_dir."public/components/libs/animate.css/");

        // Move framework assets to public libs directory
        iSM::move(self::$base_dir."vendor/framework-assets/iumio-framework/",
            self::$base_dir."public/components/libs/");
        // Move manager assets to public libs directory
        iSM::move(self::$base_dir."vendor/framework-assets/iumio-manager",
            self::$base_dir."public/components/libs/");
        // Move mercure assets to public libs directory
        iSM::move(self::$base_dir."vendor/framework-assets/mercure",
            self::$base_dir."public/components/libs/");

        // Move SKEL assets to public libs directory
        iSM::move(self::$base_dir."vendor/framework-assets/skel",
            self::$base_dir."public/components/libs/");
    }


    /**
     * Remove components dir in root directory
     * @throws \Exception
     */
    final public static function removeComponentsDir()
    {
        // remove animate.css assets to public directory
        iSM::delete(self::$base_dir."public/components/libs/animate.css/", "directory");
        // remove bootstrap assets to public directory
        iSM::delete(self::$base_dir."public/components/libs/bootstrap/", "directory");
        // remove dwr assets to public directory
        iSM::delete(self::$base_dir."public/components/libs/dwr/", "directory");
        // remove font-awesome assets to public directory
        iSM::delete(self::$base_dir."public/components/libs/font-awesome/", "directory");
        // remove jquery assets to public directory
        iSM::delete(self::$base_dir."public/components/libs/jquery/", "directory");
        // remove framework assets to public libs directory
        iSM::delete(self::$base_dir."public/components/libs/iumio-framework", "directory");
        // remove manager assets to public libs directory
        iSM::delete(self::$base_dir."public/components/libs/iumio-manager", "directory");
        // remove mercure assets to public libs directory
        iSM::delete(self::$base_dir."public/components/libs/mercure", "directory");
        // remove SKEL assets to public libs directory
        iSM::delete(self::$base_dir."public/components/libs/skel", "directory");
        // Create libs directory
        iSM::create(self::$base_dir."public/components/libs/", "directory");
    }


    /**
     * Init installer
     * @throws \Exception
     */
    final public static function do() {
        self::removeComponentsDir();
        self::moveComponentsDownloadedByComposer();
    }
}



