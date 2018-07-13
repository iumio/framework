<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iumioFramework\Core\Base\Http;

use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Requirement\Patterns\Singleton\SingletonClassicPattern;
use Josantonius\Session\Session;

/**
 * HttpSession class.
 * Manage a session
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dany Rafina <dany.rafina@iumio.com>
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 */
class HttpSession extends SingletonClassicPattern implements SessionInterfaceRequest
{
    protected $session = null;
    private static $id = null;

    /**
     * HttpSession constructor.
     * @throws
     */
    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        if (!$this->isStarted() && null === $this->session) {
            //@ob_start();

            session_start();
            $this->session = $_SESSION;

        }
    }

    /**
     * @return mixed
     */
    public static function getId()
    {
        return (session_id());
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function setId($id)
    {
        self::$id = $id;
        return (session_id($id));
    }

    /**
     * @return mixed
     */
    public static function getName()
    {
        return (session_name());
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function setName($name)
    {
        return (session_name($name));
    }

    /**
     * @param int|null $lifetime
     * @return mixed
     */
    public function invalidate($lifetime = null)
    {
        // TODO: Implement invalidate() method.
    }

    /**
     * @param bool $destroy
     * @param int|null $lifetime
     * @return mixed
     */
    public function migrate($destroy = false, $lifetime = null)
    {
        // TODO: Implement migrate() method.
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $_SESSION = $this->session;
        return (0 === count(array_diff($this->session, $_SESSION)))? true : false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function has($name)
    {
        return ((isset($this->session[$name]) && null != $this->session[$name])? true : false);
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return ((isset($this->session[$name]) && null != $this->session[$name])? $this->session[$name] : null);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
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
     * @return mixed
     */
    public function all()
    {
        return ($this->session);
    }

    /**
     * @param array $attributes
     * @return bool|null
     * @throws Server500
     */
    public function replace(array $attributes)
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
            return (true);
        }
        return (null);
    }

    /**
     * @param string $name
     * @return bool
     * @throws Server500
     */
    public function remove($name)
    {
        if (isset($this->session[$name])) {
            unset($this->session[$name]);
            $this->start();
            return (true);
        } else {
            throw new Server500(new \ArrayObject(array("explain" =>
                "iumio Session Error : Your session name is not defined", "solution" =>
                "Please check the session object with HttpSession::all instruction")));
        }
    }

    /**
     * @return mixed
     */
    public function clear()
    {
        if ($this->isStarted()) {
            return (session_destroy());
        }
        return (false);
    }

    /** Check if session is started
     * @return bool
     */
    public function isStarted()
    {
        if ('cli' !== php_sapi_name()) {
            return ((PHP_SESSION_ACTIVE === session_status()) ? true : false);
        }
        return (false);
    }

}
