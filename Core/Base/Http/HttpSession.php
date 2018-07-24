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

use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\Patterns\Singleton\SingletonClassicPattern;
use iumioFramework\Core\Server\Server;

/**
 * HttpSession class.
 * Manage a session
 * @author Dany Rafina <dany.rafina@iumio.com>
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 */
class HttpSession extends SingletonClassicPattern implements SessionInterfaceRequest
{
    /**
     * @var null|array The session oject : Initialized to null firstly
     */
    protected $session = null;

    /**
     * @var null|string The session identifier : Initialized to null firstly
     */
    private $id = null;

    /**
     * @var null|string The session name : Initialized to null firstly
     */
    private $name = null;

    /**
     * HttpSession constructor.
     * @throws
     */
    public function __construct()
    {
        $this->start();
    }

    /** Start the session
     * @return void
     * @throws Server500
     * @throws \Exception
     */
    public function start():void
    {

        if ((!$this->isStarted() && null === $this->session) || ($this->isStarted() && null === $this->session)) {
            $this->initSessionConfig();
            if (!$this->isStarted()) {
                session_start();
            }
            $this->session = $_SESSION;
        }
    }

    /** Get the session id
     * @return mixed The session id
     */
    public function getId()
    {
        return ($this->id);
    }

    /** Set a new session id
     * @param string $id The session id
     * @return mixed Return the session id
     */
    public function setId($id)
    {
        $this->id = $id;
        return (session_id($id));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return ($this->name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;
        return (session_name($name));
    }

    /**
     * Save the modification about sessions items
     * @return bool true if iumio session is the same as PHP SESSION, false for an error
     * ($_SESSION & $this->session not the same)
     */
    public function save():bool
    {
        $_SESSION = $this->session;
        return (0 === count(array_diff($this->session, $_SESSION)))? true : false;
    }

    /**
     * Check if a session key exist
     * @param string $name The session key
     * @return bool If exist or not
     */
    public function has($name):bool
    {
        return ((isset($this->session[$name]) && null != $this->session[$name])? true : false);
    }

    /** Get a session key value
     * @param string $name The session key
     * @return mixed The result (if null : not result)
     */
    public function get($name)
    {
        return ((isset($this->session[$name]) && null != $this->session[$name])? $this->session[$name] : null);
    }

    /**
     * Edit a session key
     * @param string $name Key name
     * @param mixed $value session item value
     * @return bool If session exist or not
     * @throws Server500
     */
    public function set($name, $value)
    {
        if (is_string($name)) {
            $this->session[$name] = $value;
            return ((isset($this->session[$name]) && null != $this->session[$name])? true : false);
        } else {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Session Error : Your session name is not a string value", "solution" =>
                "Please check your value type")));
        }
    }

    /**
     * Return all session items
     * @return null|array Null if session are not initialized or an array with all session item
     */
    public function all():?array
    {
        return ($this->session);
    }

    /** Replace session items
     * @param array $attributes Session item with key/value
     * @return bool false if not session item has not edited or true if it has been edited
     * @throws Server500
     */
    public function replace(array $attributes):bool
    {
        $status = false;
        foreach ($this->session as $one => $value) {
            if (null !== $this->get($one)) {
                $this->set($one, $value);
                $status = true;
            }
        }
        if (true === $status) {
            $this->save();
        }

        return ($status);
    }

    /** Remove a session item
     * @param string $name Item name
     * @return bool
     * @throws Server500
     * @throws \Exception
     */
    public function remove($name):bool
    {
        if (isset($this->session[$name])) {
            unset($this->session[$name]);
            $this->save();
            return (true);
        } else {
            throw new Server500(new \ArrayObject(array("explain" =>
                "Session Error : The session name [$name] is not defined", "solution" =>
                "Please check the session object with HttpSession::all instruction to remove".
                " the correct session item")));
        }
    }

    /**
     * Clear the session
     * @return bool If session is clear properly or not
     * @throws Server500
     */
    public function clear():bool
    {
        if (true === $this->isStarted()) {
            session_unset();
            $this->setToDefault();
            session_regenerate_id(true);
            return (empty($_SESSION)? true : false);
        }
        throw new Server500(new \ArrayObject(array("explain" => "Cannot clear the session when is not started",
            "solution" => "Please start a session instance before clear it")));
    }

    /**
     * Set all value to default
     */
    private function setToDefault():void
    {
        $this->session = null;
        $this->id = null;
        $this->name = null;
    }

    /** Init the session configuration
     * @throws Server500
     * @throws \Exception
     * @return void
     */
    private function initSessionConfig():void
    {
        if (!defined("IUMIO_ENV")) {
            throw new Server500(new \ArrayObject(array("explain" => "Framework Environment is not defined",
                "solution" => "Please initialize the framework environment")));
        }
        if (false === Server::exist(IUMIO_ROOT."/elements/cache/".strtolower(IUMIO_ENV)."/sessions")) {
            Server::create(IUMIO_ROOT."/elements/cache/".
                strtolower(IUMIO_ENV)."/sessions", "directory");
        }

        $this->id = session_id();
        $this->name = session_name();
        if (!$this->isStarted()) {
            session_save_path(IUMIO_ROOT . '/elements/cache/' . strtolower(IUMIO_ENV) . '/sessions');
            ini_set('session.gc_probability', 1);
        }
    }


    /** Check if session is started
     * @return bool
     */
    public function isStarted():bool
    {
        return ((PHP_SESSION_ACTIVE === session_status()) ? true : false);
    }
}
