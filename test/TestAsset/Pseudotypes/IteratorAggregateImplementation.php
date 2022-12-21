<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

use IteratorAggregate;
use Traversable;

/** @template-implements IteratorAggregate<array-key, mixed> */
class IteratorAggregateImplementation implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
    }
}
