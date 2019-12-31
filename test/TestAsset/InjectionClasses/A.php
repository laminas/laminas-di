<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\InjectionClasses;

class A
{
    public $bs = array();

    public function addB(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectBOnce(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectBTwice(B $b)
    {
        $this->bs[] = $b;
    }

    public function injectSplitDependency(B $b, $somestring)
    {
        $b->id = $somestring;
        $this->bs[] = $b;
    }
}
