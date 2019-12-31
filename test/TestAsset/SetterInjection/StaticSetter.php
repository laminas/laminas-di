<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\SetterInjection;

class StaticSetter
{
    /**
     * @var string
     */
    public static $name = 'originalName';

    /**
     * @param string $name
     */
    public static function setName($name)
    {
        self::$name = $name;
    }

    /**
     *
     */
    public function setFoo()
    {
    }
}
