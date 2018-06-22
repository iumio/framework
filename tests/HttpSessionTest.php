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


namespace iumioFramework\Tests;

use iumioFramework\Core\Base\Http\HttpSession;
use PHPUnit\Framework\TestCase;

/**
 * Class ServerTest
 * @package iumioFramework\Tests
 */
class HttpSessionTest extends TestCase
{
    /**
     * Test create an instance of HttpSession
     * @throws \Exception
     */
    public function testCreateInstance()
    {
        $instance = HttpSession::getInstance();
        $instance->set("test", "value1");
        $instance->save();
        $this->assertInstanceOf("iumioFramework\Core\Base\Http\HttpSession", $instance);
        $this->assertTrue(isset($_SESSION["test"]));
        $this->assertEquals($_SESSION["test"], "value1");
    }
}
