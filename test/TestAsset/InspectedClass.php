<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

class InspectedClass
{
    public function __construct($foo, $baz)
    {
        $this->foo = $foo;
        $this->baz = $baz;
    }
}
