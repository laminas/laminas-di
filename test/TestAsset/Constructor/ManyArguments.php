<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

class ManyArguments
{
    public $result;

    public function __construct(
        $a = null,
        $b = null,
        $c = null,
        $d = null,
        $e = null,
        $f = null
    ) {
        $this->result = array_filter(compact('a', 'b', 'c', 'd', 'e', 'f'), function ($value) {
            return ($value !== null);
        });
    }
}
