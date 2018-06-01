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


namespace iumioFramework\Core\Additional\Manager\Display;

use iumioFramework\Core\Additional\Manager\Display\Style\ColorManager as Color;

/**
 * Class OutputManager
 * @package iumioFramework\Core\Additional\Manager\Display
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class OutputManager
{

    /**
     * @var null|string Color for manager
     */
    static protected $managerColor = null;

    /** display Success Message
     * @param string $message Message to display
     * @param string $exit Exit script
     * @param bool $header If header is display
     */
    final public static function displayAsSuccess(string $message, string $exit = "yes", bool $header = true)
    {
        $colors = self::getManagerColorInstance();
        echo "\n\n".$colors->getColoredString($message, "green", "transparent", $header);
        if ($exit == "yes") {
            exit();
        }
    }

    /** display Notice Message
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    final public static function displayAsNotice(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        echo "\n\n".$colors->getColoredString($message, "yellow", "transparent");
        if ($exit == "yes") {
            exit();
        }
    }

    /** display Error Message
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    final public static function displayAsError(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        echo "\n\n".$colors->getColoredString($message, "red", "transparent");
        if ($exit == "yes") {
            exit();
        }
    }

    /** display As Normal Message
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    public static function displayAsNormal(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        echo "\n\n".$colors->getColoredString(
            $message,
            "green",
            "transparent",
            false
        );
        if ($exit == "yes") {
            exit();
        }
    }

    /** display As black with transparent background
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    public static function displayAsBlack(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        echo "\n\n".$colors->getColoredString($message, "black", "transparent", false);
        if ($exit == "yes") {
            exit();
        }
    }


    /** display As black with transparent background
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    public static function displayAsNoColor(string $message, string $exit = "yes")
    {
        echo "\n\n".$message;
        if ($exit == "yes") {
            exit();
        }
    }

    /** display As Green Message with transparent background
     * @param string $message Message to display
     * @param string $exit Exit script
     * @param bool Include the FCM header
     */
    public static function displayAsGreen(string $message, string $exit = "yes", bool $header = true)
    {
        $colors = self::getManagerColorInstance();
        echo $colors->getColoredString($message, "green", "transparent", $header);
        if ($exit == "yes") {
            exit();
        }
    }

    /** display As Red Message with transparent background
     * @param string $message Message to display
     * @param string $exit Exit script
     * @param bool Include the FCM header
     */
    public static function displayAsRed(string $message, string $exit = "yes", bool $header = true)
    {
        $colors = self::getManagerColorInstance();
        echo $colors->getColoredString($message, "red", "transparent", $header);
        if ($exit == "yes") {
            exit();
        }
    }

    /** display for read line Message
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    public static function displayAsReadLine(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        echo "\n".$colors->getColoredStringReadLine($message, "black", "transparent");
        if ($exit == "yes") {
            exit();
        }
    }

    /** display for end Success Message
     * @param string $message Message to display
     * @param string $exit Exit script
     */
    public static function displayAsEndSuccess(string $message, string $exit = "yes")
    {
        $colors = self::getManagerColorInstance();
        self::clear();
        echo "\n".$colors->getColoredString($message, "green", "transparent");
        if ($exit == "yes") {
            exit();
        }
    }

    /** Get Color instance
     * @return Color|string Color instance
     */
    final protected static function getManagerColorInstance()
    {
        return((self::$managerColor == null)? self::$managerColor = new Color() : self::$managerColor);
    }

    /** Clear the command line text
     * @return bool As a success
     */
    final public static function clear():bool
    {
        echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; // ^[H^[J;
        return (true);
    }
}
