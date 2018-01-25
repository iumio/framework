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

namespace iumioFramework\Core\Console\Module\Help;

use iumioFramework\Additional\Manager\Module\ModuleManager;
use iumioFramework\Core\Console\CoreManager;
use iumioFramework\Core\Console\Module\ModuleManagerInterface;
use iumioFramework\Core\Console\Display\OutputManager as Output;
use iumioFramework\Core\Console\ComManager as File;

/**
 * Class HelpManager
 * @package iumioFramework\Core\Console\Module\Help
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class HelpManager extends ModuleManager implements ModuleManagerInterface
{
    protected $options;

    /**
     * @return mixed|void
     * @param $options
     * @throws \Exception
     */
    public function __render(array $options)
    {
        $this->options = $options;

        if (!isset($options["commands"])) {
            Output::displayAsError("Help Manager Error : Option is not exist. 
            Referer to help command to get options list\n");
        }

        $opt = $options["commands"] ?? null;
        $f = File::getFileCommand();
        if ($f == null) {
            Output::displayAsError("Help Manager Error: Command File is empty ", "none");
        }
        $commands = $f->commands;
        if (count($opt) === 1) {
            $space = 50;
            Output::displayAsSuccess("Framework Console Manager Helper", "no");
            $str = "\033[0;32m"."Hey, this is the available commands\n\n". "\033[0m";
            $str .= "\033[0;32m"."Usage : ".$options["global"]["executable"]." ".$options["global"]["manager"].
                " command [options] \n\n\n". "\033[0m";
            foreach ($commands as $command => $val) {
                if ($val->type === "multiple") {
                    $s = $val->args;
                    $nstr = "";
                    $nopt = "";
                    foreach ($s as $one) {
                        $opt = "    - ".$one->name;
                        $nstr .= "\033[0;34m" .$opt.$this->putSpace($opt, $space). "\033[0m". $one->desc  . "\n";
                        $nstr .= "      "."\033[0;33musage : " .$one->usage. "\033[0m\n\n";
                    }

                    if (isset($val->options)) {
                        foreach ($val->options as $one) {
                            $opt = "    " . $one->name;
                            $nopt .= "\033[0;34m" . $opt . $this->putSpace($opt, $space) . "\033[0m" .
                                $one->desc . "\n";
                            $nopt .= "      " . "\033[0;33musage : " . $one->usage . "\033[0m\n\n";
                        }
                    }

                    $str .= "\033[0;35m" .$command . "\033[0m" ;
                    $str = "\033[0;32m" . $str  . "\033[0m" . $this->putSpace($command, $space) .
                        "" . $val->desc . "\n".
                        "\033[0;33musage : " .$val->usage. "\033[0m".(($nopt != "")?".\n  options:\n$nopt" : "").
                        "\n  sub-commands : \n".$nstr."\n\n\n";

                }
                else {
                    $str .= "\033[0;35m" .$command . "\033[0m" ;
                    $str = "\033[0;32m" . $str  . "\033[0m" . $this->putSpace($command, $space) .
                        "" . $val->desc . "\n".
                        "\033[0;33musage : " .$val->usage. "\033[0m\n\n\n";
                }
            }
            Output::displayAsNoColor($str, "none");
        } else {
            $opt = $options["commands"][1] ?? null;
            if ($opt == null) {
                throw new \Exception("Help Manager Error : No option are available");
            }

            $i = 0;

            foreach ($commands as $command => $val) {
                if ($opt == $command) {
                    $str = "";
                    $space = 50;
                    if ($val->type === "multiple") {
                        $s = $val->args;
                        $nstr = "";
                        $nopt = "";
                        foreach ($s as $one) {
                            $opt = "    - ".$one->name;
                            $nstr .= "\033[0;34m" .$opt.$this->putSpace($opt, $space). "\033[0m". $one->desc  . "\n";
                            $nstr .= "      "."\033[0;33musage : " .$one->usage. "\033[0m\n\n";
                        }

                        if (isset($val->options)) {
                            foreach ($val->options as $one) {
                                $opt = "    " . $one->name;
                                $nopt .= "\033[0;34m" . $opt . $this->putSpace($opt, $space) . "\033[0m" . $one->desc . "\n";
                                $nopt .= "      " . "\033[0;33musage : " . $one->usage . "\033[0m\n\n";
                            }
                        }

                        $str .= "\033[0;35m" .$command . "\033[0m" ;
                        $str = "\033[0;32m" . $str  . "\033[0m" . $this->putSpace($command, $space) .
                            "" . $val->desc . "\n".
                            "\033[0;33musage : " .$val->usage. "\033[0m".(($nopt != "")?".\n  options:\n$nopt" : "").
                            "\n  sub-commands : \n".$nstr."\n\n\n";

                    }
                    else {
                        $str .= "\033[0;35m" .$command . "\033[0m" ;
                        $str .= $this->putSpace($command, $space) .
                            "" . $val->desc . "\n".
                            "\033[0;33musage : " .$val->usage. "\033[0m\n\n\n";
                    }
                    Output::displayAsNoColor($str, "yes");
                    break;
                }
            }
            if ($i == 0) {
                Output::displayAsError("Command [$opt] not found", "yes");
            }
        }
    }

    /**
     * Calc how many space can be contain a string to align them
     * @param string $str String element
     * @param int $max Max space
     * @return string The required spaces
     */
    private function putSpace(string $str, int $max):string {
        $i = 0;
        $len = strlen($str);
        $max -= $len;
        $space = "";
        while ($i <= $max) {
            $space .= " ";
            $i++;
        }
        return ($space);
    }

    public function __alter()
    {
        // TODO: Implement __alter() method.
    }


    /**
     * HelpManager constructor.
     */
    public function __construct()
    {
        CoreManager::setCurrentModule("Help Manager");
    }
}
