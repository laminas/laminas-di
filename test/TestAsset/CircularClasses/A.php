<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class A
{
    public function __construct(B $b)
    {
    }
}
