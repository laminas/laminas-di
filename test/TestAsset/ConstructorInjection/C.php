<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\ConstructorInjection;

class C
{
    public $a = null;
    public $params = array();
    public function __construct(A $a, array $params = array())
    {
        $this->a = $a;
        $this->params = $params;
    }
}
