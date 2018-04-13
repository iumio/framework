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

use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Base\Renderer\Renderer;
use iumioFramework\Core\Requirement\FrameworkServices\AppConfig;
use iumioFramework\Core\Server\Server;
use iumioFramework\Core\Additional\Zip\ZipEngine;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Masters\MasterCore;
use iumioFramework\Core\Base\Json\JsonListener as JL;
use iumioFramework\Core\Requirement\Environment\FEnv;

/**
 * Class AppsMaster
 * @package iumioFramework\Core\Manager
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class AppsMaster extends MasterCore
{

    /**
     * Going to app manager
     * @throws
     */
    public function appsActivity()
    {
        return ($this->render("appmanager", array("selected" => "appmanager", "loader_msg" => "Apps Manager")));
    }

    /**
     * Going to base app manager
     * @throws
     */
    public function baseAppsActivity()
    {
        return ($this->render("baseappmanager", array("selected" => "baseappmanager",
            "loader_msg" => "Base Apps Manager")));
    }


    /**
     * Get all apps
     * @return \stdClass $file Apps
     * @throws
     */
    public function getAllApps():\stdClass
    {
        $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($file as $one) {
            $one->link_edit_save = $this->generateRoute(
                "iumio_manager_app_manager_edit_save_app",
                array("appname" => $one->name),
                null,
                true
            );

            $one->link_auto_dis_ena = $this->generateRoute(
                "iumio_manager_app_manager_auto_dis_ena_app",
                array("appname" => $one->name),
                null,
                true
            );

            $one->link_remove = $this->generateRoute(
                "iumio_manager_app_manager_remove_app",
                array("appname" => $one->name),
                null,
                true
            );

            $one->link_export = $this->generateRoute(
                "iumio_manager_app_manager_export_app",
                array("appname" => $one->name),
                null,
                true
            );

            $one->link_config = $this->generateRoute(
                "iumio_manager_app_manager_get_config_app",
                array("appname" => $one->name),
                null,
                true
            );
        }
        return ($file);
    }

    /** Get app statistics
     * @return array App statistics
     */
    public function getStatisticsApp():array
    {

        $f = $this->getAllApps();
        $fc = 0;
        $fenable = 0;
        $fprefix = 0;

        foreach ($f as $one) {
            if ($one->enabled == "yes") {
                $fenable++;
            }
            if ($one->prefix != "") {
                $fprefix++;
            }
            $fc++;
        }

        return (array("number" => $fc, "prefixed" => $fprefix, "enabled" => $fenable));
    }

    /**
     * Get all simple app
     * @throws
     */
    public function getSimpleAppsActivity():Renderer
    {
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK", "results" => $this->getAllApps())));
    }


    /** Switch app to default
     * @param string $appname App name
     * @return Renderer
     * @throws Server500
     */
    public function switchDefaultActivity(string $appname):Renderer
    {
        $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($file as $one => $val) {
            if ($val->isdefault == "yes") {
                $val->isdefault = "no";
                $val->update = new \DateTime('UTC');
            }
            if ($val->name == $appname) {
                $val->update = new \DateTime();
                $val->isdefault = "yes";
            }
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        JL::put(FEnv::get("framework.config.core.apps.file"), $file);
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }

    /** auto change enabled or disabled app
     * @param string $appname App name
     * @return Renderer
     * @throws
     */
    public function autoDisabledOrEnabledActivity(string $appname):Renderer
    {
        $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($file as $one => $val) {
            if ($val->name == $appname) {
                if ($val->enabled == "yes") {
                    $val->enabled = "no";
                } elseif ($val->enabled == "no") {
                    $val->enabled = "yes";
                }
                $val->update = new \DateTime();
            }
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        JL::put(FEnv::get("framework.config.core.apps.file"), $file);
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }

    /** remove one app
     * @param string $appname App name
     * @return Renderer Renderer value
     * @throws Server500
     * @throws \Exception
     *
     */
    public function removeActivity(string $appname):Renderer
    {
        $removeapp = false;
        $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($file as $one => $val) {
            if ($val->name == $appname) {
                unset($file->$one);
                $removeapp = true;
                break;
            }
        }

        if ($removeapp == false) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "App does not exist")));
        }
        $file = array_values((array)$file);
        $filetemp = $file;
        $file = json_encode((object) $file, JSON_PRETTY_PRINT);
        $assets = $this->getMaster("Assets");
        $assets->clear($appname, "all");
        JL::put(FEnv::get("framework.config.core.apps.file"), $file);
        $this->removeComposerApp($appname);
        Server::delete(FEnv::get("framework.apps").$appname, "directory");
        $msg = "OK";
        if (empty($filetemp)) {
            $base = __DIR__."/../../../../../../";
            $e = json_decode(file_get_contents($base."elements/config_files/core/framework.config.json"));
            $e->installation = null;
            $e->deployment = null;
            file_put_contents($base."elements/config_files/core/framework.config.json", json_encode($e));
            $msg = "RELOAD";
        }
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => $msg)));
    }


    /**
     * export one app
     * @param string $appname App name
     * @return Renderer
     * @throws Server500
     */
    public function exportActivity(string $appname):Renderer
    {
        $appconfig = null;
        $file = JL::open(FEnv::get("framework.config.core.apps.file"));
        foreach ($file as $one => $val) {
            if ($val->name == $appname) {
                $appconfig = $val;
                break;
            }
        }
        if ($appconfig == null) {
            throw new Server500(new \ArrayObject(array("The application $appname does not exist.",
                "Please check your app configuration")));
        }
        unset($appconfig->enabled);

        try {
            $date = new \DateTime();
            $datefull = $date;
            $date = $date->format('YmdHi');
            $dirbase = FEnv::get("framework.bin").'exports/';
            $dirapp = $dirbase.($appname).'/';
            $dirappexp  = $dirapp.($appname)."_".$date.'/';
            Server::create($dirbase, 'directory');
            Server::create($dirapp, 'directory');
            Server::create($dirappexp, 'directory');
            JL::put($dirappexp."register.json", json_encode($appconfig, JSON_PRETTY_PRINT));
            $zip = new ZipEngine($dirapp.($appname)."_".$date.".zip");
            $zip->setSource(FEnv::get("framework.apps").$appname);
            $zip->addFile($dirappexp."register.json", "register.json");
            $zip->setArchiveComment("$appname - Export date :".$datefull->format('g:ia \o\n l jS F Y'));
            $zip->recursiveCompress();
            if ($zip->close()) {
                Server::delete($dirappexp, 'directory');
                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
            } else {
                return ((new Renderer())->jsonRenderer(array("code" => 500,
                    "msg" => "Error on archive creation process")));
            }
        } catch (\Exception $e) {
            return ((new Renderer())->jsonRenderer(array("code" => 500,
                "msg" => "Error on archive creation process : ".$e->getMessage())));
        }
    }


    /**
     * import one app
     * @return Renderer
     * @throws Server500
     * @throws \Exception
     */
    public function importActivity():Renderer
    {
        $sourcePath = $_FILES['file']['tmp_name'];
        if ("zip" != pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "Your package must be a zip package")));
        }
        $date = new \DateTime();
        $datex = $date->format('ymdhis').rand(0, 34);
        @Server::create(FEnv::get("framework.bin").'import/', 'directory');
        $fileex = FEnv::get("framework.bin").'import/'.$datex.'.zip';
        move_uploaded_file($sourcePath, FEnv::get("framework.bin").'import/'.$datex.'.zip');
        $inf = pathinfo($fileex);
        if (isset($inf['extension']) && $inf['extension'] == "zip") {
            try {
                $zip = new ZipEngine($fileex);
                $zip->extractTo(FEnv::get("framework.bin").'import/'.$datex);
                $f =  JL::open(FEnv::get("framework.bin").'import/'.$datex.'/register.json');
                if (empty($f)) {
                    return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "Missing file register.json")));
                }
                $appname = $f->name;

                $fa = json_decode(file_get_contents(
                    FEnv::get("framework.root")."elements/config_files/core/apps.json"));
                $lastapp = 0;
                foreach ($fa as $one => $val) {
                    if ($val->name == $appname) {
                        return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "App already exist")));
                    }
                    $lastapp++;
                }


                Server::copy(FEnv::get("framework.bin").'import/'.$datex,
                    FEnv::get("framework.apps").$appname, 'directory');
                Server::delete(FEnv::get("framework.apps").$appname.'/register.json', 'file');
                Server::delete(FEnv::get("framework.bin").'import/'.$datex, 'directory');
                $zip->close();
                Server::delete(FEnv::get("framework.bin").'import/'.$datex.'.zip', 'file');

                $fa->$lastapp = new \stdClass();
                $fa->$lastapp->name = $appname;
                $fa->$lastapp->enabled = "no";
                $fa->$lastapp->prefix = trim(stripslashes($f->prefix));
                $fa->$lastapp->class = $f->class;
                $ndate = new \DateTime('UTC');
                $fa->$lastapp->creation = $ndate;
                $fa->$lastapp->update = $ndate;
                $fa = json_encode($fa, JSON_PRETTY_PRINT);
                JL::put(FEnv::get("framework.root")."elements/config_files/core/apps.json", $fa);

                JL::close(FEnv::get("framework.root")."/elements/config_files/core/apps.json");

                $this->addComposerApp($appname);

                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK", "ext" =>
                    "The application ".$appname. " is installed.")));
            } catch (\Exception $e) {
                return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                    "Your package is not a valid iumio app package")));
            }
        } else {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "Your package must be a zip package")));
        }
        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }

    /** Create one app
     * @return Renderer JSON render
     * @throws Server500
     * @throws \Exception
     */
    public function createActivity():Renderer
    {
        $name = $this->get("request")->get("name");
        $enable = $this->get("request")->get("enabled");
        $prefix = $this->get("request")->get("prefix");
        $template = $this->get("request")->get("template");

        if ($prefix != "" && $this->checkPrefix($prefix) == -1) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                "Error on app prefix. (App prefix must be a string without special character exepted [ _ & numbers])"))
            );
        }

        if (!in_array($enable, array("yes", "no"))) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "App name already exist")));
        }

        if (trim($name) == "") {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "Error on app parameters")));
        }

        if (file_exists(FEnv::get("framework.apps").$name)) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "App name already exist")));
        }


        $temdirbase = FEnv::get("framework.fcm")."Module/App/AppTemplate";

        $tempdir = ($template == "no")? $temdirbase.'/notemplate/{appname}/' : $temdirbase.'/template/{appname}/';
        Server::copy($tempdir, FEnv::get("framework.apps").$name, 'directory');
        $napp = FEnv::get("framework.apps").$name;

        // APP
        $f = file_get_contents($napp."/{appname}.php.local");
        $str = str_replace("{appname}", $name, $f);
        file_put_contents($napp."/{appname}.php.local", $str);
        rename($napp."/{appname}.php.local", $napp."/".$name.".php");

        // Mercure
        $f = file_get_contents($napp."/Routing/default.merc");
        $str = str_replace("{appname}", $name, $f);
        file_put_contents($napp."/Routing/default.merc", $str);

        // MASTER
        $f = file_get_contents($napp."/Masters/DefaultMaster.php.local");
        $str = str_replace("{appname}", $name, $f);
        file_put_contents($napp."/Masters/DefaultMaster.php.local", $str);
        rename($napp."/Masters/DefaultMaster.php.local", $napp."/Masters/DefaultMaster.php");

        // REGISTER TO APP CORE
        $f = (JL::open(FEnv::get("framework.root")."elements/config_files/core/apps.json"));
        $lastapp = 0;
        foreach ($f as $one => $val) {
            $lastapp++;
        }

        $f->$lastapp = new \stdClass();
        $f->$lastapp->name = $name;
        $f->$lastapp->enabled = $enable;
        $f->$lastapp->prefix = trim(stripslashes($prefix));
        $f->$lastapp->class = "\\".$name."\\".$name;
        $ndate = new \DateTime('UTC');
        $f->$lastapp->creation = $ndate;
        $f->$lastapp->update = $ndate;
        $f = json_encode($f, JSON_PRETTY_PRINT);
        JL::put(FEnv::get("framework.root")."elements/config_files/core/apps.json", $f);
        if ($template == "yes") {
            $assets = $this->getMaster("Assets");
            $assets->publish($name, "dev");
        }

        $this->addComposerApp($name);

        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }


    /** Check prefix response
     * @param string $res response
     * @return int Is valid prefix response
     */
    final private function checkPrefix(string $res):int
    {
        if (!preg_match('/[\/\'^£$%&*()}{@#~?><>,|=+¬-]/', $res)) {
            return (1);
        }
        return (-1);
    }

    /** edit one app
     * @param string $appname App name
     * @return Renderer JSON render
     * @throws \Exception
     */
    public function editActivity(string $appname):Renderer
    {
        $prefix = $this->get("request")->get("prefix");
        $enable = $this->get("request")->get("enabled");


        // Advanced options
        $vdev = $this->get("request")->get("vdev");
        $vprod = $this->get("request")->get("vprod");

        $hostsdeva = $this->get("request")->get("hostsdeva");
        $hostsdevd = $this->get("request")->get("hostsdevd");
        $hostsproda = $this->get("request")->get("hostsproda");
        $hostsprodd = $this->get("request")->get("hostsprodd");

        if ($prefix != "" && $this->checkPrefix($prefix) == -1) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                "Error on app prefix. (App prefix must be a string without special character exepted [ _ & numbers])"))
            );
        }

        if (!in_array($enable, array("yes", "no"))) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" => "App name already exist")));
        }

        if (!in_array($vdev, array("null", "false", "true"))) {
            return ((new Renderer())->jsonRenderer(array("code" => 500,
                "msg" => "Cannot set visibility on dev environment : Undefined value $vdev")));
        }

        if (!in_array($vprod, array("null", "false", "true"))) {
            return ((new Renderer())->jsonRenderer(array("code" => 500,
                "msg" => "Cannot set visibility on prod environment : Undefined value $vdev")));
        }

        $f = json_decode(file_get_contents(
            FEnv::get("framework.root")."elements/config_files/core/apps.json"));

        foreach ($f as $one => $val) {
            if ($val->name == $appname) {
                $val->prefix = trim(stripslashes($prefix));
                $val->enabled = trim($enable);
                $val->update = new \DateTime('UTC');
                break;
            }
        }
        $f = json_encode($f, JSON_PRETTY_PRINT);
        file_put_contents(FEnv::get("framework.root")."elements/config_files/core/apps.json", $f);


        if ("null" === $vdev) {
            $vdev = null;
        }
        elseif ("false" === $vdev) {
            $vdev = false;
        }
        elseif ("true" === $vdev) {
            $vdev = true;
        }
        else {
            $vdev = null;
        }

        if ("null" === $vprod) {
            $vprod = null;
        }
        elseif ("false" === $vprod) {
            $vprod = false;
        }
        elseif ("true" === $vprod) {
            $vprod = true;
        }
        else {
            $vprod = null;
        }

        $hostsdeva  = (("" === trim($hostsdeva))? null : array_map('trim',
            (explode(";", $hostsdeva))));
        $hostsdevd  = (("" === trim($hostsdevd))? null : array_map('trim',
            (explode(";", $hostsdevd))));
        $hostsproda = (("" === trim($hostsproda))? null : array_map('trim',
            (explode(";", $hostsproda))));
        $hostsprodd = (("" === trim($hostsprodd))? null : array_map('trim',
            (explode(";", $hostsprodd))));




        if (JsonListener::exists(FEnv::get("app.config.file", $appname))) {
            $a = json_decode(file_get_contents(
                FEnv::get("app.config.file", $appname)));
        }
        else {
            $a = new \stdClass();
        }

        $a->visibility_dev  = $vdev;
        $a->visibility_prod = $vprod;

        $a->hosts_allowed_dev   = $hostsdeva;
        $a->hosts_denied_dev    = $hostsdevd;
        $a->hosts_allowed_prod  = $hostsproda;
        $a->hosts_denied_prod   = $hostsprodd;

        $a = json_encode($a, JSON_PRETTY_PRINT);
        file_put_contents(FEnv::get("app.config.file", $appname), $a);

        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }

    /** Get the app configuration
     * @param string $appname The app name
     * @return Renderer Json Renderer
     * @throws Server500
     */
    public function getAppConfigActivity(string $appname) {
        $g = AppConfig::getInstance($appname);
        return ((new Renderer())->jsonRenderer(array("code" => 200, "result" =>  $g->getConfig())));
    }

    /** Adding into composer.json the app class and path
     * @param string $name App name
     * @throws Server500 if JsonListener failed
     */
    private function addComposerApp(string $name) {
        $composer = JL::open(FEnv::get("framework.root")."composer.json");
        $composer->autoload->{"psr-4"}->{$name."\\"} = "apps/$name";
        JL::put(FEnv::get("framework.root")."composer.json",
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }


    /** Removing into composer.json the app class and path
     * @param string $name App name
     * @throws Server500 if JsonListener failed
     */
    private function removeComposerApp(string $name) {
        $composer = JL::open(FEnv::get("framework.root")."composer.json");
        unset($composer->autoload->{"psr-4"}->{$name."\\"});
        JL::put(FEnv::get("framework.root")."composer.json",
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
