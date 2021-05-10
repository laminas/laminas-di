<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

class E
{
    public function __construct(C $c)
    {
    }
}
