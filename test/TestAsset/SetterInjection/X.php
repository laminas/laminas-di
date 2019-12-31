<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\SetterInjection;

class X
{
    public $one = null;
    public $two = null;
    public function setOne($one)
    {
        $this->one = $one;
    }
    public function setTwo($two)
    {
        $this->two = $two;
    }
}
