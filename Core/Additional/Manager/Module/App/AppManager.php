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

namespace iumioFramework\Core\Additional\Manager\Module\App;

use iumioFramework\Core\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Server\Server as Server;
use iumioFramework\Core\Base\Json\JsonListener;
use iumioFramework\Core\Additional\Manager\CoreManager;
use iumioFramework\Core\Additional\Manager\Module\Assets\AssetsManager;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Additional\Manager\FEnvFcm;
use iumioFramework\Core\Additional\Manager\Module\ModuleManagerInterface;
use iumioFramework\Core\Additional\Manager\Module\App\OutputManagerOverride as Output;

/**
 * Class AppManager
 * @package iumioFramework\Core\Additional\Manager\Module\App
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class AppManager extends ModuleManager implements ModuleManagerInterface
{
    protected $options;
    protected $stage = array(
        "Application name (like DefaultApp --> end with App): ",
        "You can install a default template with your app. Would you like to have one ? (yes/no) - Tap Enter for yes: ",
        "Yeah! Would you like to enabled your app ? (yes/no) -  Tap Enter for yes:",
        "Informations are correct ? (yes/no) - Tap Enter for yes:",
        "Deleting your app means all files and directories in your app will be deleted.
         Are you sure to confirm this action ? (yes/no) - Tap Enter for yes:",
        "Ok. I process to deleting your app ...",
        "Deleting action is aborted ",
        "Your app list. To select one, please enter the number matching with app",
        "Enter your app prefix? (App prefix must be a string without special character except [ _ & numbers])
         -  Tap Enter for no app prefix:"
    );
    protected $params = array("appname" => "", "template" => "", "isdefault" => "", "correct" => "",
        "applist" => array(), "capp" => "");

    /**
     * @return mixed|void
     * @param $options
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;
        if (!isset($options["commands"])) {
            Output::outputAsError("App Manager Error \n  You must to specify an option\n");
        }

        $opt = $options["commands"][0] ?? null;

        if ($opt == "app:create") {
            $this->stepNewProject();
        } elseif ($opt == "app:remove") {
            $this->stepRemoveProject();
        } elseif ($opt == "app:enabled") {
            $this->stepEnabledProject();
        } elseif ($opt == "app:disabled") {
            $this->stepDisabledProject();
        } else {
            Output::outputAsError("App Manager Error \n
                   This command doesn't exist. Referer to help comannd\n");
        }
    }

    /** Check app name format
     * @param string $appname App name
     * @return int Is valid app name
     */
    final protected function checkAppName(string $appname):int
    {
        if ($appname == "App" || strpos($appname, "App") == false ||
            preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $appname)) {
            return (-1);
        }
        return (1);
    }

    /** Check boolean response
     * @param string $res response
     * @return int Is valid boolean response
     */
    final protected function checkBooleanResponse(string $res):int
    {
        if (trim($res) == "yes" || trim($res) == "no" || trim($res) == "") {
            return (1);
        }
        return (-1);
    }

    /** Check prefix response
     * @param string $res response
     * @return int Is valid prefix response
     */
    final protected function checkPrefix(string $res):int
    {
        if (!preg_match('/[\/\'^£$%&*()}{@#~?><>,|=+¬-]/', $res)  &&
            strpos($res, "\\") == false) {
            return (1);
        }
        return (-1);
    }

    /** Check if app name exist
     * @param string $appname App name
     * @return bool If app exist
     * @throws Server500
     */
    final protected function checkAppExist(string $appname):bool
    {
        if (file_exists(FEnvFcm::get("framework.apps").$appname)) {
            return (true);
        }
        return (false);
    }

    /** Listen the STDIN
     * @return string STDIN value
     */
    final protected function listener():string
    {
        while ($resp = rtrim(fgets(STDIN))) {
            return $resp;
        }
        return ('');
    }


    /** Create an app
     * @throws Server500
     * @throws \Exception
     */
    final protected function stepNewProject()
    {
        Output::clear();
        Output::displayAsGreen("Welcome on App Manager.\n".
            "I'm assist you to create your new app. Many question will ask you.", "none");
        Output::outputAsReadLine($this->stage[0], "none");

        $this->params['appname'] = ucfirst($this->listener());

        $elem = false;
        while ($elem == false) {
            if ($this->checkAppName($this->params['appname']) != 1) {
                Output::displayAsRed("Your app name is invalid. Please Retry.", "none", false);
            } elseif ($this->checkAppExist($this->params['appname']) == true) {
                $elem = false;
                Output::displayAsRed(
                    "This app name is already exist, Please enter an other app name.",
                    "none",
                    false
                );
            } else {
                $elem = true;
                continue;
            }

            Output::outputAsReadLine($this->stage[0], "none");
            $this->params['appname'] = ucfirst($this->listener());
        }
        Output::displayAsGreen(
            "Great! Your app name is ".$this->params['appname'],
            "none",
            false
        );
        Output::outputAsReadLine($this->stage[1], "none");
        $this->params['template'] = $this->listener();
        while ($this->checkBooleanResponse($this->params['template']) != 1) {
            Output::displayAsRed("Invalid response. Please Retry (yes/no)", "none", false);
            Output::outputAsReadLine($this->stage[1], "none");
            $this->params['template'] = $this->listener();
        }
        Output::outputAsReadLine($this->stage[2], "none");
        $this->params['enabled'] = $this->listener();
        while ($this->checkBooleanResponse($this->params['enabled']) != 1) {
            Output::displayAsRed("Invalid response. Please Retry (yes/no)", "none", false);
            Output::outputAsReadLine($this->stage[2], "none");
            $this->params['enabled'] = $this->listener();
        }

        Output::outputAsReadLine($this->stage[8], "none");
        $this->params['prefix'] = strtolower($this->listener());
        while ($this->checkPrefix($this->params['prefix']) != 1) {
            Output::displayAsRed("Invalid response. Please Retry (yes/no)", "none", false);
            Output::outputAsReadLine($this->stage[8], "none");
            $this->params['prefix'] = strtolower($this->listener());
        }

        $this->params['template'] = (($this->params['template'] != "")? $this->params['template'] : "yes");
        $this->params['enabled'] = (($this->params['enabled'] != "")? $this->params['enabled'] : "yes");
        $this->showRecap();
        Output::outputAsReadLine($this->stage[3], "none");
        $this->params['correct'] = $this->listener();
        while ($this->checkBooleanResponse($this->params['correct']) != 1) {
            Output::displayAsRed("Invalid response. Please Retry", "none", false);
            Output::outputAsReadLine($this->stage[3], "none");
            $this->params['correct'] = $this->listener();
        }
        if ($this->params['correct'] == "no") {
            Output::displayAsRed("Creation Aborted. Please re-run app and enter the correct informations");
        }
        $this->createAppProcess();
    }

    /**
     * Remove an app
     * @throws
     */
    final protected function stepRemoveProject()
    {
        Output::clear();
        Output::displayAsGreen("Welcome on App Manager.\n".
            "I'm assist you to remove your app. Many question will ask you.", "none");
        Output::outputAsReadLine($this->stage[0], "none");

        $this->params['appname'] = ucfirst($this->listener());

        $elem = false;
        while ($elem == false) {
            if ($this->checkAppName($this->params['appname']) != 1) {
                Output::displayAsRed("Your app name is invalid. Please Retry.", "none", false);
            } elseif ($this->checkAppExist($this->params['appname']) == false &&
                $this->checkAppRegister($this->params['appname']) == false) {
                $elem = false;
                Output::displayAsRed(
                    "This app not exist, Please enter an existed app name",
                    "none",
                    false
                );
            } else {
                $elem = true;
                continue;
            }

            Output::outputAsReadLine($this->stage[0], "none");
            $this->params['appname'] = ucfirst($this->listener());
        }

        if ($this->checkAppExist($this->params['appname']) == true &&
            $this->checkAppRegister($this->params['appname']) == false) {
            Output::displayAsGreen("Ok ! Your app is ".$this->params['appname'].
                ". It exist on apps directory but it's not declared in app declarator", "none", false);
        } elseif ($this->checkAppExist($this->params['appname']) == false &&
            $this->checkAppRegister($this->params['appname']) == true) {
            Output::displayAsGreen("Ok ! Your app is ".$this->params['appname'].
                ". It's declared in app declarator but not exist in apps directory", "none", false);
        } else {
            Output::displayAsGreen("Ok ! Your app is ".$this->params['appname'].
                ". It's declared in app declarator and exist in apps directory", "none", false);
        }
        Output::outputAsReadLine($this->stage[4], "none");
        $conf = ((!empty($this->listener()))?  $this->listener() : "yes");
        while ($this->checkBooleanResponse($conf) != 1) {
            Output::displayAsRed("Invalid response. Please Retry (yes/no)", "none", false);
            Output::outputAsReadLine($this->stage[4], "none");
            $conf = ((!empty($this->listener()))?  $this->listener() : "yes");
        }
        if ($conf == "no") {
            Output::outputAsError($this->stage[6]);
        }
        $this->removeAppProcess();
    }

    /**
     * @throws Server500
     */
    final protected function stepEnabledProject()
    {
        Output::clear();
        Output::displayAsGreen("Welcome on App Manager.\n".
            "I'm assist you to enabled your app. Many question will ask you.\n".$this->stage[7], "none");
        if ($this->showDisabledAppsRegister() == false) {
            return ;
        }
        Output::outputAsReadLine("Which number ? : ", "none");
        $this->params['capp'] = $this->listener();

        while (!@isset($this->params['applist'][$this->params['capp'] - 1])) {
            Output::displayAsRed("Your choose is incorrect. Please Retry", "none", false);
            $this->showDisabledAppsRegister();
            Output::outputAsReadLine("Which number ? : ", "none");
            $this->params['capp'] = $this->listener();
        }
        $this->params['capp'] = $this->params['applist'][$this->params['capp'] - 1];

        Output::displayAsGreen(
            "Ok ! You choose ".$this->params['capp']." to be enabled",
            "none",
            false
        );
        $this->enabledAppProcess();
    }


    /**
     * Disable an app
     * @throws Server500
     */
    final protected function stepDisabledProject()
    {
        Output::clear();
        Output::displayAsGreen("Welcome on App Manager.\n".
            "I'm assist you to disabled your app. Many question will ask you.\n".$this->stage[7], "none");
        if ($this->showEnabledAppsRegister() == false) {
            return ;
        }
        Output::outputAsReadLine("Which number ? : ", "none");
        $this->params['capp'] = $this->listener();

        while (!is_numeric($this->params['capp']) || !isset($this->params['applist'][$this->params['capp'] - 1])) {
            Output::displayAsRed("Your choose is incorrect. Please retry", "none", false);
            $this->showEnabledAppsRegister();
            Output::outputAsReadLine("Which number ? : ", "none");
            $this->params['capp'] = $this->listener();
        }
        $this->params['capp'] = $this->params['applist'][$this->params['capp'] - 1];

        Output::displayAsGreen(
            "Ok ! You choose ".$this->params['capp']." to be disabled",
            "none",
            false
        );
        $this->disabledAppProcess();
    }

    /**
     * Show recap
     */
    final protected function showRecap()
    {
        $strOutput = "Summary of actions : \n";
        $strOutput .= "----------------------------\n";
        $strOutput .= "    - Name : ".$this->params['appname']." \n";
        $strOutput .= "    - Template : ".$this->params['template']." \n";
        $strOutput .= "    - Enabled : ".$this->params['enabled']. "\n";
        $strOutput .= "    - Prefix : ".(($this->params['prefix'] == "")?
                "no prefix" : "/".trim(stripslashes($this->params['prefix'])));
        Output::displayAsGreen($strOutput, "none", false);
    }

    /** Check if app is registered to apps.json
     * @param string $appname App name
     * @return bool If exist or not
     * @throws Server500
     */
    final protected function checkAppRegister(string $appname):bool
    {
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));
        if (empty($file)) {
            return (false);
        }
        foreach ($file as $val) {
            if ($val->name == $appname) {
                return (true);
            }
        }
        return (false);
    }


    /** Show all app in apps.json
     * @return bool
     * @throws Server500
     */
    final protected function showAppsRegister()
    {
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));
        $i = 1;
        if (count($file) == 0) {
            Output::outputAsError("Ops! You have no app registered. Please create an app with app");
        }
        $str = "";
        foreach ($file as $val) {
            $str .= $i.") ".$val->name.(($val->enabled == "yes")? " : Enabled" : "Disabled")."\n";
            array_push($this->params['applist'], $val->name);
            $i++;
        }

        Output::outputAsNormal("Your apps \n------------\n".$str, "none");
        return (false);
    }


    /** Show enabled app in apps.json
     * @return int
     * @throws Server500
     */
    final protected function showEnabledAppsRegister():int
    {
        $this->params['applist'] = array();
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));
        $i = 1;
        if ((is_string($file) && strlen($file) < 3) || (count((array) $file) == 0)) {
            Output::outputAsError("Ops! You do not have an enabled application.");
            return (false);
        }
        $str = "";
        foreach ($file as $val) {
            if ($val->enabled == "yes") {
                $str .= $i . ") " . $val->name . " : Enabled"  . "\n";
                array_push($this->params['applist'], $val->name);
                $i++;
            }
        }

        if (count($this->params['applist']) == 0) {
            Output::displayAsRed("Ops! You do not have an enabled app.");
            return (false);
        } else {
            Output::displayAsGreen("Your apps \n------------\n".$str, "none", false);
        }
        return (true);
    }


    /** Show disabled app in apps.json
     * @return int
     * @throws Server500
     */
    final protected function showDisabledAppsRegister():int
    {
        $this->params['applist'] = array();
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));
        $i = 1;
        if ((is_string($file) && strlen($file) < 3) || (count((array)  $file) == 0)) {
            Output::displayAsRed("Ops! You do not have a disabled app.");
            return (false);
        }
        $str = "";
        foreach ($file as $val) {
            if ($val->enabled == "no") {
                $str .= $i . ") " . $val->name . " : Disabled" . "\n";
                array_push($this->params['applist'], $val->name);
                $i++;
            }
        }
        if (count($this->params['applist']) == 0) {
            Output::displayAsRed("Ops! You do not have a disabled app.");
            return (false);
        } else {
            Output::displayAsGreen("Your apps \n------------\n".$str, "none", false);
        }
        return (true);
    }

    /**
     * Processing to create app
     * @throws \Exception
     */
    final protected function createAppProcess()
    {
        $appname = $this->params['appname'];
        Output::outputAsReadLine("Processing to create app : $appname", "none");
        sleep(1);
        $temp = $this->params['template'];
        $temdirbase = __DIR__."/AppTemplate";
        $tempdir = ($temp == "no")? $temdirbase.'/notemplate/{appname}/' : $temdirbase.'/template/{appname}/';
        Server::copy($tempdir, FEnvFcm::get("framework.apps").$appname, 'directory');
        $napp = FEnvFcm::get("framework.apps").$appname;

        // APP
        $file = file_get_contents($napp."/{appname}.php.local");
        $str = str_replace("{appname}", $appname, $file);
        file_put_contents($napp."/{appname}.php.local", $str);
        rename($napp."/{appname}.php.local", $napp."/$appname.php");

        // Mercure
        $file = file_get_contents($napp."/Routing/default.merc");
        $str = str_replace("{appname}", $appname, $file);
        file_put_contents($napp."/Routing/default.merc", $str);

        // MASTER
        $file = file_get_contents($napp."/Masters/DefaultMaster.php.local");
        $str = str_replace("{appname}", $appname, $file);
        file_put_contents($napp."/Masters/DefaultMaster.php.local", $str);
        rename($napp."/Masters/DefaultMaster.php.local", $napp."/Masters/DefaultMaster.php");

        // REGISTER TO APP CORE
        $file = JsonListener::open(FEnvFcm::get("framework.config.core.apps.file"));
        if (empty($file)) {
            $file = new \stdClass();
        }

        $lastapp = count((array) $file);

        $file->$lastapp = new \stdClass();
        $file->$lastapp->name = $this->params['appname'];
        $file->$lastapp->enabled = (($this->params['enabled'] != "")? $this->params['enabled'] : "yes");
        $file->$lastapp->prefix = trim(stripslashes($this->params['prefix']));
        $file->$lastapp->class = "\\".$this->params['appname']."\\".$this->params['appname'];
        $ndate = new \DateTime('UTC');
        $file->$lastapp->creation = $ndate;
        $file->$lastapp->update = $ndate;
        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(FEnvFcm::get("framework.config.core.apps.file"), $file);
        $this->initialJSON();
        if ($this->params['template'] == "yes") {
            $asm = new AssetsManager();
            $asm->__render(["commands" => ["assets:copy"], "options" => ["--symlink", "--noexit", "--appname=".
                $this->params['appname']]]);
        }

        // $this->addComposerApp($this->params['appname']);
        Output::displayAsGreen("Your app is ready to use. To test your app,
         go to project location on your browser with parameter ".(($this->params['prefix'] != "")?
                "/".trim(stripslashes($this->params['prefix'])) : "")."/index. Enjoy !", "none", false);
    }


    /**
     * Build framework.config.json
     * @throws Server500
     */
    final protected function initialJSON()
    {
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.config.file")));
        if (isset($file->installation) && $file->installation == null) {
            $file->installation = new \DateTime();
            $file->default_env =  "dev";
            $file->location = realpath(__DIR__."../../../../../../../../").DIRECTORY_SEPARATOR;
            $result = json_encode($file, JSON_PRETTY_PRINT);
            file_put_contents(FEnvFcm::get("framework.config.core.config.file"), $result);
        }
    }

    /**
     * Processing to enabled an app
     * @throws Server500
     */
    final protected function enabledAppProcess()
    {
        $appname = $this->params['capp'];
        Output::displayAsGreen(
            "Processing to enabled app : $appname  will be enabled \n",
            "none",
            false
        );
        sleep(1);
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));

        foreach ($file as $val) {
            if ($val->name == $this->params['capp']) {
                $val->update = new \DateTime();
                $val->enabled = "yes";
            }
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(FEnvFcm::get("framework.config.core.apps.file"), $file);
        Output::displayAsGreen("Now, the ".$this->params['capp']." is enabled", "none", false);
    }

    /**
     * Processing to disabled an app
     * @throws Server500
     */
    final protected function disabledAppProcess()
    {
        $appname = $this->params['capp'];
        Output::displayAsGreen(
            "Processing to enabled app : $appname  will be enabled \n",
            "none",
            false
        );
        sleep(1);
        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));

        foreach ($file as $val) {
            if ($val->name == $this->params['capp']) {
                $val->update = new \DateTime();
                $val->enabled = "no";
            }
        }

        $file = json_encode($file, JSON_PRETTY_PRINT);
        file_put_contents(FEnvFcm::get("framework.config.core.apps.file"), $file);
        Output::displayAsGreen("Now, the ".$this->params['capp']." is disabled.", "none", false);
    }


    /**
     * Processing to remove app
     * @throws Server500
     * @throws \Exception
     */
    final protected function removeAppProcess()
    {
        $appname = $this->params['appname'];
        Output::displayAsGreen("Processing to delete app : $appname \n", "none", false);
        sleep(1);

        // DELETE TO APP CORE

        $file = json_decode(file_get_contents(FEnvFcm::get("framework.config.core.apps.file")));
        if (!empty($file)) {
            foreach ($file as $one => $val) {
                if ($val->name == $appname) {
                    unset($file->$one);
                    break;
                }
            }

            $file = array_values((array)$file);
            $filetemp = $file;
            $file = json_encode((object) $file, JSON_PRETTY_PRINT);

            file_put_contents(FEnvFcm::get("framework.config.core.apps.file"), $file);
            if (strlen($file) < 3) {
                file_put_contents(FEnvFcm::get("framework.config.core.apps.file"), "");
            }
        }

        $asm = new AssetsManager();
        $asm->__render(["commands" => ["assets:clear"],
            "options" => ["--quiet", "--noexit", "--appname=". $this->params['appname']]]);
        // $this->removeComposerApp($appname);
        Server::delete(FEnvFcm::get("framework.apps")."$appname", "directory");
        if (empty($filetemp)) {
            $base = __DIR__."/../../../../../../";
            $elem = json_decode(file_get_contents($base."elements/config_files/core/framework.config.json"));
            $elem->installation = null;
            $elem->deployment = null;
            file_put_contents($base."elements/config_files/core/framework.config.json", json_encode($elem));
        }
        Output::displayAsGreen("The application has been deleted. To create a new application,
         use [app create] .", "none", false);
    }

    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    /**
     * AppManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("App Manager");
    }
}
