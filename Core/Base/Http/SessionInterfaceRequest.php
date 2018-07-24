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

namespace iumioFramework\Core\Base\Http;

/**
 * Interface for the session.
 *
 * @author RAFINA Dany <dany.rafina@iumio.com>
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 */
interface SessionInterfaceRequest
{
    /**
     * Starts the session.
     *
     */
    public function start():void;

    /**
     * Returns the session ID.
     *
     * @return string The session ID
     */
    public function getId();

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Returns the session name.
     *
     * @return mixed The session name
     */
    public function getName();

    /**
     * Sets the session name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Save the current session.
     * @return bool
     */
    public function save():bool ;

    /**
     * Checks if an attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return bool true if the attribute is defined, false otherwise
     */
    public function has($name);

    /**
     * Returns a session item.
     *
     * @param string $name    The attribute name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Sets an session item.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value);

    /**
     * Returns all session items.
     *
     * @return array|null Attributes
     */
    public function all():?array;

    /**
     * Edit session items.
     *
     * @param array $attributes Session item with key/value
     * @return bool false if not session item has not edited or true if it has been edited
     */
    public function replace(array $attributes):bool;

    /** Remove a session item
     * @param string $name Item name
     * @return bool
     * @throws \Exception
     */
    public function remove($name):bool;

    /**
     * Clear the session
     * @return bool If session is clear properly or not
     */
    public function clear():bool;

    /** Check if session is started
     * @return bool
     */
    public function isStarted():bool;
}
