<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

class OptionalArg
{
    public function __construct($param = null)
    {
        $this->param = $param;
    }

    public function inject($param1 = null, $param2 = null)
    {
    }
}
