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
error_reporting(E_ALL);
use iumioFramework\Core\Base\Http\HttpSession;
use PHPUnit\Framework\TestCase;

/**
 * Class ServerTest
 * @package iumioFramework\Tests
 */
class HttpSessionTest extends TestCase
{

    /**
     * Test if session is started
     * @throws \Exception
     */
    public function testIsStarted()
    {
        $instance = HttpSession::getInstance();
        $this->assertTrue($instance->isStarted());
    }

    /**
     * Test create session item
     * @throws \Exception
     */
    public function testCreate()
    {
        $instance = HttpSession::getInstance();
        $instance->set("test", "value1");
        $instance->save();
        $this->assertInstanceOf("iumioFramework\Core\Base\Http\HttpSession", $instance);
        $this->assertNotNull($instance->get("test"));
        $this->assertEquals($instance->get("test"), "value1");
        $this->assertTrue(isset($_SESSION["test"]));
        $this->assertEquals($_SESSION["test"], "value1");
    }

    /**
     * Test remove a session item
     * @throws \Exception
     */
    public function testRemove()
    {
        $instance = HttpSession::getInstance();
        $instance->set("test", "value1");
        $instance->save();
        $this->assertInstanceOf("iumioFramework\Core\Base\Http\HttpSession", $instance);
        $this->assertNotNull($instance->get("test"));
        $this->assertEquals($instance->get("test"), "value1");
        $instance->remove("test");
        $instance->save();
        $this->assertNull($instance->get("test"));
        $this->assertFalse(isset($_SESSION["test"]));
    }

    /**
     * Test if a session item exist
     * @throws \Exception
     */
    public function testHas()
    {
        $instance = HttpSession::getInstance();
        $instance->set("test", "value1");
        $instance->save();
        $this->assertTrue($instance->has("test"));
        $instance->remove("test");
        $instance->save();
        $this->assertFalse($instance->has("test"));
    }

    /**
     * Test get all session item
     * @throws \Exception
     */
    public function testAll()
    {
        $arr =  ["test" => "val1", "test2" => "val2", "test3" => "val3"];
        $instance = HttpSession::getInstance();
        foreach ($arr as $one => $value) {
            $instance->set($one, $value);
        }
        $instance->save();
        $all = $instance->all();

        foreach ($arr as $one => $value) {
            $this->assertTrue(isset($all[$one]));
            $this->assertEquals($value, $all[$one]);
            $instance->remove($one);
        }

        $instance->save();
        $this->assertEmpty($instance->all());
    }

    /**
     * Test set id for a session
     * @throws \Exception
     */
    public function testId()
    {
        HttpSession::setId("123");
        $instance = HttpSession::getInstance();
        $this->assertEquals("123", session_id());
        $this->assertEquals("123", $instance->getId());
    }

    /**
     * Test destroy a session
     * @throws \Exception
     */
    public function testDestroy()
    {
        $instance = HttpSession::getInstance();
        $this->assertTrue($instance->clear());
    }


    /**
     * Test set name for session
     * @throws \Exception
     */
    public function testName()
    {
        HttpSession::setName("test");
        $instance = HttpSession::getInstance();
        $this->assertEquals("test", session_name());
        $this->assertEquals("test", $instance->getName());
    }


    /**
     * Test replace a session item
     * @throws \Exception
     */
    public function testReplace()
    {
        $arr =  ["test" => "val1", "test2" => "val2", "test3" => "val3"];
        $instance = HttpSession::getInstance();
        foreach ($arr as $one => $value) {
            $instance->set($one, $value);
        }
        $instance->save();
        $all = $instance->all();

        foreach ($arr as $one => $value) {
            $this->assertTrue(isset($all[$one]));
            $this->assertEquals($value, $all[$one]);
        }

        $arr =  ["test" => "aze", "test2" => "azer", "test3" => "azert"];
        foreach ($arr as $one => $value) {
            $instance->set($one, $value);
        }
        $instance->save();
        $all = $instance->all();

        foreach ($arr as $one => $value) {
            $this->assertTrue(isset($all[$one]));
            $this->assertEquals($value, $all[$one]);
            $instance->remove($one);
        }

        $instance->save();
        $this->assertEmpty($instance->all());
    }
}
