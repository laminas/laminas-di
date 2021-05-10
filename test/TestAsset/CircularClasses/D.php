<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class D
{
    public function __construct(E $e)
    {
    }
}
