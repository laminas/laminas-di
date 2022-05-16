<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

use Countable;
use Iterator;

class IntersectionTypeConstructorDependency
{
    public function __construct(private Iterator&Countable $iter)
    {
    }
}
