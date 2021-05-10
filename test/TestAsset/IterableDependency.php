<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

class IterableDependency
{
    public function __construct(iterable $iterator)
    {
    }
}
