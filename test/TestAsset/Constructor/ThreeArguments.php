<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\Constructor;

class ThreeArguments
{
    public $result;

    public function __construct(
        $a = null,
        $b = null,
        $c = null
    ) {
        $this->result = compact('a', 'b', 'c');
    }
}
