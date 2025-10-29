<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array-key, mixed>
 * @final This class should not be extended and will be marked final in version 4.0
 */
class IteratorAggregateImplementation implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
    }
}
