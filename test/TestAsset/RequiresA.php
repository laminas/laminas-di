<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

class RequiresA
{
    public function __construct(A $p)
    {
    }
}
