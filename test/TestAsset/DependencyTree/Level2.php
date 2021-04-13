<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

class Level2
{
    /** @var mixed */
    public $optionalResult;

    /** @param null|mixed $opt*/
    public function __construct($opt = null)
    {
        $this->optionalResult = $opt;
    }
}
