<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\CallbackClasses;

class B
{
    public $c, $params = null;

    public static function factory(C $c, array $params = array())
    {
        $b = new B();
        $b->c = $c;
        $b->params = $params;
        return $b;
    }

    protected function __construct()
    {
        // no dice
    }
}
