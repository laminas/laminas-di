<?php

// phpcs:disable

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class Y
{
    // @codingStandardsIgnoreStart
    public function __construct(Y $y = null)
    {
    }
}
