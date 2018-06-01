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

namespace iumioFramework\Core\Additional\Manager\Module;

/**
 * Interface ModuleManagerInterface
 * @package iumioFramework\Core\Manager\Module
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

interface ModuleManagerInterface
{
    /**
     * Display result in prompt
     * @param array $options Method parameters
     * @return mixed
     */
    public function __render(array $options);

    /** Alter function
     * @return mixed
     */
    public function __alter();

    /**
     * ModuleManager constructor.
     */
    public function __construct();
}
