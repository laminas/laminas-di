<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\Constructor;

class Php7
{
    public function __construct(string $stringDep, int $numDep, callable $callbacDep)
    {
    }
}
