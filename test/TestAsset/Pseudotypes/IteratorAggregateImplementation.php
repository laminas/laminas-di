<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

use IteratorAggregate;
use Traversable;

class IteratorAggregateImplementation implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
    }
}
