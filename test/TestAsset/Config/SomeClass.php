<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Config;

use LaminasTest\Di\TestAsset\A;
use LaminasTest\Di\TestAsset\B;

class SomeClass
{
    public function __construct(A $a, B $b)
    {
    }
}
