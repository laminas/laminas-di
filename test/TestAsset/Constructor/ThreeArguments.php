<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

class ThreeArguments
{
    /** @var array<string, mixed> */
    public $result;

    /**
     * @param null|mixed $a
     * @param null|mixed $b
     * @param null|mixed $c
     */
    public function __construct(
        $a = null,
        $b = null,
        $c = null
    ) {
        $this->result = ['a' => $a, 'b' => $b, 'c' => $c];
    }
}
