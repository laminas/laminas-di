<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

class OptionalArguments
{
    /**
     * @param null|string $foo
     * @param string      $bar
     */
    public function __construct($foo = null, $bar = 'something')
    {
    }
}
