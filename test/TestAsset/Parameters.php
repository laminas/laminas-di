<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

class Parameters
{
    public function general($a, B $b, $c = 'something')
    {
    }

    public function typehintRequired(A $foo)
    {
    }

    public function typelessRequired($bar)
    {
    }

    public function typehintOptional(A $fooOpt = null)
    {
    }

    public function typelessOptional($flag = false)
    {
    }
}
