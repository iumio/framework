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

namespace iumioFramework\Core\Requirement;

use iumioFramework\Core\Server\Server;
use iumioFramework\Core\Requirement\Environment\FEnv;

/**
 *
 * Class BaseApp
 * @package iumioFramework\Core\Requirement
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class BaseApp extends App
{
    /**
     * Save an App
     * @throws \Exception
     */
    public function save()
    {
        $appname = $this->name;
        $temp = "no";
        $temdirbase = FEnv::get("framework.root").
            "vendor/iumio-framework/Core/Additional/Manager/Module/App/AppTemplate";
        $tempdir = ($temp == "no")? $temdirbase.'/notemplate/{appname}/' : $temdirbase.'/template/{appname}/';
        Server::copy($tempdir, FEnv::get("framework.root")."apps/".$appname, 'directory');
        $napp = FEnv::get("framework.root")."apps/".$appname;

        // APP
        $f = file_get_contents($napp."/{appname}.php.local");
        $str = str_replace("{appname}", $appname, $f);
        file_put_contents($napp."/{appname}.php.local", $str);
        rename($napp."/{appname}.php.local", $napp."/$appname.php");

        // Mercure
        $f = file_get_contents($napp."/Routing/default.merc");
        $str = str_replace("{appname}", $appname, $f);
        file_put_contents($napp."/Routing/default.merc", $str);

        // MASTER
        $f = file_get_contents($napp."/Masters/DefaultMaster.php.local");
        $str = str_replace("{appname}", $appname, $f);
        file_put_contents($napp."/Masters/DefaultMaster.php.local", $str);
        rename($napp."/Masters/DefaultMaster.php.local", $napp."/Masters/DefaultMaster.php");

        // REGISTER TO APP CORE
        $f = json_decode(file_get_contents(FEnv::get("framework.root").
            "elements/config_files/core/apps.json"));
        $lastapp = 0;
        foreach ($f as $one => $val) {
            $lastapp++;
        }
        if ($this->params['isdefault'] == "yes") {
            foreach ($f as $one => $val) {
                if ($val->isdefault == "yes") {
                    $val->isdefault = "no";
                    break;
                }
            }
        }
        $f->$lastapp = new \stdClass();
        $f->$lastapp->name = $this->params['appname'];
        $f->$lastapp->isdefault = $this->params['isdefault'];
        $f->$lastapp->class = "\\".$this->params['appname']."\\".$this->params['appname'];
        $f = json_encode($f, JSON_PRETTY_PRINT);
        file_put_contents(FEnv::get("framework.root")."elements/config_files/core/apps.json", $f);
        if ($this->params['template'] == "yes") {
            new AM(array("core/manager", "assets-manager", "--copy", "--appname=". $this->params['appname'],
                "--symlink", "--noexit"));
        }
        Output::outputAsSuccess("\n Your app is ready to use. To test your app go to project location on your
         browser with parameter /hello. Enjoy ! \n", "none");
    }

    /**
     * Delete an app
     * @throws
     */
    public function remove()
    {
        $f = json_decode(file_get_contents(FEnv::get("framework.root").
            "elements/config_files/core/apps.json"));
        foreach ($f as $one => $val) {
            if ($val->name == $this->name) {
                unset($f->$one);
                break;
            }
        }

        $f = json_encode($f, JSON_PRETTY_PRINT);
        file_put_contents(FEnv::get("framework.root")."elements/config_files/core/apps.json", $f);

        Server::delete(FEnv::get("framework.root")."apps/".$this->name, "directory");
        Server::delete(FEnv::get("framework.root")."public/components/apps/".strtolower($this->name),
            'directory');
    }
}
