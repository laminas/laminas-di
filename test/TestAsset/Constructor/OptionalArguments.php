<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
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
