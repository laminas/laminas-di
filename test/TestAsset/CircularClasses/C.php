<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class C
{
    public function __construct(D $d)
    {
    }
}
