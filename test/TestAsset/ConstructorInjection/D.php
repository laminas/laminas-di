<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\ConstructorInjection;

use LaminasTest\Di\TestAsset\DummyInterface;

class D
{
    protected $d = null;
    public function __construct(DummyInterface $d)
    {
        $this->d = $d;
    }
}
