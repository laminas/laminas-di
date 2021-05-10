<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class B
{
    public function __construct(A $a)
    {
    }
}
