<?php

declare(strict_types=1);

namespace LaminasTest\Di\Classes;

use Countable;
use Iterator;

class IntersectionTypeConstructorDependency
{
    public function __construct(private Iterator&Countable $iter)
    {
    }
}
