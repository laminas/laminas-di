<?php

// phpcs:disable

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class X
{
    // @codingStandardsIgnoreStart
    public function __construct(X $x)
    {
    }
}
