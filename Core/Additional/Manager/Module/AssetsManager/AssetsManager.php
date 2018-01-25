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

namespace iumioFramework\Core\Console\Module\Assets;

use iumioFramework\Core\Additionnal\Server\ServerManager as Server;
use iumioFramework\Core\Console\CoreManager;
use iumioFramework\Exception\Server\Server500;
use iumioFramework\Core\Console\Module\ModuleManagerInterface;
use iumioFramework\Core\Console\Display\OutputManager as Output;
use iumioFramework\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Console\FEnvFcm;

/**
 * Class AssetsManager
 * @package iumioFramework\Core\Console\Module\Assets
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class AssetsManager extends ModuleManager implements ModuleManagerInterface
{
    protected $l;

    /**
     * @return mixed|void
     * @param $options
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;
        if (!isset($options["commands"])) {
            Output::displayAsRed("Option is not exist. Referer to help command to get options list\n");
        }

        $opt = $options["commands"][0] ?? null;

            if ($opt == "assets:clear") {
                $this->clearAssets($this->options);
            } elseif ($opt == "assets:copy") {
                $this->copyAssets($this->options);
            } else {
                Output::displayAsRed("This command doesn't exist. Referer to help command\n");
            }
    }

    /** Clear some assets in web directory
     * @param array $options Clear options
     * @return null
     * @throws Server500
     * @throws \Exception
     */
    public function clearAssets(array $options)
    {
        // TO DO HERE
        $appname = null;
        if ($this->strlikeInArray("--appname", $options["options"]) != null) {
            $ch = $this->strlikeInArray("--appname", $options["options"]);
            $e = explode("=", $ch);
            $appname = $e[1];
            if (strpos($appname, "App") == false) {
                if (!in_array("--quiet", $options["options"])) {
                    Output::displayAsRed("The app name is invalid");
                }
            }
        }
        if (!is_dir(FEnvFcm::get("framework.web.components.apps")."dev/".strtolower($appname)) ||
            !is_dir(FEnvFcm::get("framework.web.components.apps")."prod/".strtolower($appname))) {
            if (!in_array("--quiet", $options["options"])) {
                Output::displayAsRed("The app $appname is not register in web assets.\n");
            }
        }

        if ($appname != null) {
            $env = ($this->strlikeInArray("--env=", $options["options"]));
            if ($env != null) {
                $nenv = explode("=", $env);
                $env = strtolower($nenv[1]);
                if (!in_array($env, array("dev", "prod", "all"))) {
                    if (!in_array("--quiet", $options["options"])) {
                        Output::displayAsRed("The environment ".
                            strtoupper($env)." does not exist.\n");
                    } else {
                        return (null);
                    }
                }
            }

            Output::displayAsGreen("Hey, I'll clean the $appname assets for ".
                (($env != null && $env != "all")? strtoupper($env)." environment" : "all environments"), "none");
            $this->callDelCreaServer($appname, $env);
                Output::displayAsGreen("$appname assets for ".(($env != null && $env != "all")?
                        strtoupper($env)." environment" : "all environments")." have been deleted.", "yes", false);
            return (null);
        }

        $env = ($this->strlikeInArray("--env=", $options["options"]));
        if ($env != null) {
            $nenv = explode("=", $env);
            $env = strtolower($nenv[1]);
            if (!in_array($env, array("dev", "prod", "all"))) {
                if (!in_array("--quiet", $options["options"])) {
                    Output::displayAsRed("The environment ".strtoupper($env)."
                     does not exist.\n");
                } else {
                    return (null);
                }
            }
        }

        Output::displayAsGreen("Hey, I'll clean all assets in web folder for ".
            (($env != null && $env !== "all")? strtoupper($env)." environment" : "all environments"), "none", false);
        $this->callDelCreaServer('#none', $env);
        if (in_array("--env=prod", $options["options"])) {
            Output::displayAsGreen("All assets for ".(($env != null && $env !== "all")?
                    strtoupper($env)." environment" : "all environments")." have been deleted.", "none", false);
        } else {
            Output::displayAsGreen("All assets for ".(($env != null && $env !== "all")?
                    strtoupper($env)." environment" : "all environments")." have been deleted.", "yes", false);
        }
        return (null);
    }


    /** Copy asset manager
     * @param array $options
     * @return null
     * @throws Server500
     * @throws \Exception
     */
    public function copyAssets(array $options)
    {

        $appname = '#none';
        $symlink = false;

        if (in_array("--symlink", $options["options"])) {
            $symlink = true;
        }
        if ($this->strlikeInArray("--appname", $options["options"]) != null) {
            $ch = $this->strlikeInArray("--appname", $options["options"]);
            $e = explode("=", $ch);
            $appname = $e[1];
            if (strpos($appname, "App") == false) {
                Output::displayAsRed("The app name is invalid");
            }

            if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$appname)) {
                Output::displayAsRed("Assets Manager Error: App $appname doesn't exist.", "yes", false);
            }
        }

        $env = ($this->strlikeInArray("--env=", $options["options"]));
        if ($env != null) {
            $nenv = explode("=", $env);
            $env = strtolower($nenv[1]);
            if (!in_array($env, array("dev", "prod", "all"))) {
                if (!in_array("--quiet", $options["options"])) {
                    Output::displayAsRed(" The environment ".
                        strtoupper($env)." does not exist.\n", "yes");
                } else {
                    return (null);
                }
            }
        }
        Output::displayAsGreen("Hey, I'll copy ".(($appname != '#none')? $appname." assets (" :
                'all assets (').(($env != null && $env !== "all")? strtoupper($env)." environment)" :
                "all environments)")." in web folder".(($symlink == true)? ' with symlink option' : ''), "none");
        $this->callDelCreaServer($appname, $env);
        $this->copy($symlink, $appname, $env);
        if ($this-> strlikeInArray("--noexit", $options["options"]) != null) {
            Output::displayAsGreen("The copy of the assets".(($appname == '#none')? '' :
                    ' for '.$appname)." has been done.", "none", false);
        } else {
            Output::displayAsGreen("The copy of the assets".(($appname == '#none')? '' :
                    ' for '.$appname)." has been done.", "yes", false);
        }
    }

    /** Call Server delete and create function
     * @param string $appname Appn ame name
     * @param string $env Environment name
     * @throws Server500
     * @throws \Exception
     */
    private function callDelCreaServer(string $appname, string $env = null)
    {
        if ($appname == '#none' && $env == null) {
            Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/dev/",
                'directory');
            Server::create(FEnvFcm::get("framework.root")."/public/components/apps/dev/",
                'directory');
            Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/prod/",
                'directory');
            Server::create(FEnvFcm::get("framework.root")."/public/components/apps/prod/",
                'directory');
        } elseif ($appname == '#none' && in_array($env, array("dev", "prod", "all"))) {
            if ($env == "all") {
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/dev/",
                    'directory');
                Server::create(FEnvFcm::get("framework.root")."/public/components/apps/dev/",
                    'directory');
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/prod/",
                    'directory');
                Server::create(FEnvFcm::get("framework.root")."/public/components/apps/prod/",
                    'directory');
            } else {
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/".
                    strtolower($env)."/", 'directory');
                Server::create(FEnvFcm::get("framework.root")."/public/components/apps/".
                    strtolower($env)."/", 'directory');
            }
        } elseif ($appname !== '#none' && in_array($env, array("dev", "prod", "all"))) {
            if ($env == "all") {
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/dev/".
                    strtolower($appname), 'directory');
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/prod/".
                    strtolower($appname), 'directory');
            } else {
                Server::delete(FEnvFcm::get("framework.root")."/public/components/apps/".
                    strtolower($env)."/".
                    strtolower($appname), 'directory');
            }
        } elseif ($appname !== '#none' && $env == null) {
            Server::delete(
                FEnvFcm::get("framework.root") . "/public/components/apps/dev/" . strtolower($appname),
                'directory'
            );
            Server::delete(
                FEnvFcm::get("framework.root") . "/public/components/apps/prod/" . strtolower($appname),
                'directory'
            );
        } else {
            Output::displayAsRed("App name or env name does not exist. Referer to help command\n");
        }
    }

    /** Process to copy assets
     * @param bool $symlink Is symlink
     * @param string|NULL $appname App name
     * @param string|null $env Environment
     * @throws Server500
     * @throws \Exception
     */
    public function copy(bool $symlink, string $appname, string $env = null)
    {
        if ($appname == '#none' && $env == null) {
            $dirs = scandir(FEnvFcm::get("framework.root")."/apps/");

            foreach ($dirs as $dir) {
                if ($dir == ".") {
                    continue;
                }
                if ($dir == "..") {
                    continue;
                }
                if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$dir)) {
                    continue;
                }
                if (file_exists(FEnvFcm::get("framework.root")."/apps/".$dir."/Front/Resources/")) {
                    Server::copy(
                        FEnvFcm::get("framework.root") . "/apps/" . $dir . "/Front/Resources/",
                        FEnvFcm::get("framework.root") . "/public/components/apps/dev/" . strtolower($dir),
                        'directory',
                        $symlink
                    );
                    Server::copy(
                        FEnvFcm::get("framework.root") . "/apps/" . $dir . "/Front/Resources/",
                        FEnvFcm::get("framework.root") . "/public/components/apps/prod/" . strtolower($dir),
                        'directory',
                        $symlink
                    );
                }
                
            }
        } elseif ($appname == '#none' && in_array($env, array("dev", "prod", "all"))) {
            if ($env == "all") {
                $dirs = scandir(FEnvFcm::get("framework.root")."/apps/");

                foreach ($dirs as $dir) {
                    if ($dir == ".") {
                        continue;
                    }
                    if ($dir == "..") {
                        continue;
                    }
                    if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$dir)) {
                        continue;
                    }
                    if (file_exists(FEnvFcm::get("framework.root")."/apps/".$dir."/Front/Resources/")) {
                        Server::copy(
                            FEnvFcm::get("framework.root") . "/apps/" . $dir . "/Front/Resources/",
                            FEnvFcm::get("framework.root") . "/public/components/apps/dev/" .
                            strtolower($dir),
                            'directory',
                            $symlink
                        );
                        Server::copy(
                            FEnvFcm::get("framework.root") . "/apps/" . $dir . "/Front/Resources/",
                            FEnvFcm::get("framework.root") . "/public/components/apps/prod/" .
                            strtolower($dir),
                            'directory',
                            $symlink
                        );
                    }
                }
            } else {
                $dirs = scandir(FEnvFcm::get("framework.root")."/apps/");

                foreach ($dirs as $dir) {
                    if ($dir == ".") {
                        continue;
                    }
                    if ($dir == "..") {
                        continue;
                    }
                    if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$dir)) {
                        continue;
                    }
                    if (file_exists(FEnvFcm::get("framework.root")."/apps/".$dir."/Front/Resources/")) {
                        Server::copy(
                            FEnvFcm::get("framework.root") . "/apps/" . $dir . "/Front/Resources/",
                            FEnvFcm::get("framework.root") . "/public/components/apps/" .
                            strtolower($env) . "/" . strtolower($dir),
                            'directory',
                            $symlink
                        );
                    }
                }
            }
        } elseif ($appname !== '#none' && in_array($env, array("dev", "prod", "all"))) {
            if ($env == "all") {
                if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$appname."/Front/Resources/")) {
                    Output::displayAsRed("The resources folder for $appname doesn't exist.");
                }
                if (file_exists(FEnvFcm::get("framework.root") . "/apps/" . $appname .
                    "/Front/Resources/")) {
                    Server::copy(
                        FEnvFcm::get("framework.root") . "/apps/" . $appname . "/Front/Resources/",
                        FEnvFcm::get("framework.root") . "/public/components/apps/dev/" .
                        strtolower($appname),
                        'directory',
                        $symlink
                    );
                    Server::copy(
                        FEnvFcm::get("framework.root") . "/apps/" . $appname . "/Front/Resources/",
                        FEnvFcm::get("framework.root") . "/public/components/apps/prod/" .
                        strtolower($appname),
                        'directory',
                        $symlink
                    );
                }
            } else {
                if (file_exists(FEnvFcm::get("framework.root") .
                    "/apps/" . $appname . "/Front/Resources/")) {
                    Server::copy(
                        FEnvFcm::get("framework.root") . "/apps/" . $appname . "/Front/Resources/",
                        FEnvFcm::get("framework.root") . "/public/components/apps/" . strtolower($env) .
                        "/" . strtolower($appname),
                        'directory',
                        $symlink
                    );
                }
            }
        } elseif ($appname !== '#none' && $env == null) {
            if (!is_dir(FEnvFcm::get("framework.root")."/apps/".$appname."/Front/Resources/")) {
                Output::displayAsRed("The resources folder for $appname doesn't exist.");
            }
            if (file_exists(FEnvFcm::get("framework.root") . "/apps/" .
                $appname . "/Front/Resources/")) {
                Server::copy(
                    FEnvFcm::get("framework.root") . "/apps/" . $appname . "/Front/Resources/",
                    FEnvFcm::get("framework.root") . "/public/components/apps/dev/" . strtolower($appname),
                    'directory',
                    $symlink
                );
                Server::copy(
                    FEnvFcm::get("framework.root") . "/apps/" . $appname . "/Front/Resources/",
                    FEnvFcm::get("framework.root") . "/public/components/apps/prod/" . strtolower($appname),
                    'directory',
                    $symlink
                );
            }
        } else {
            Output::displayAsRed("App name or env name does not exist. Referer to help command\n");
        }
    }

    public function __alter()
    {
        // TODO: Implement __alter() method.
    }

    /**
     * AssetsManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("Assets Manager");
    }
}
