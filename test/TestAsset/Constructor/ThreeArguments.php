<?php

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
