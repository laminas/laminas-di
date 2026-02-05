<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

use Iterator;
use ReturnTypeWillChange;

/**
 * @template-implements Iterator<array-key, mixed>
 * @final This class should not be extended and will be marked final in version 4.0
 */
class IteratorImplementation implements Iterator
{
    #[ReturnTypeWillChange]
    public function current()
    {
    }

    public function next(): void
    {
    }

    #[ReturnTypeWillChange]
    public function key()
    {
    }

    public function valid(): bool
    {
    }

    public function rewind(): void
    {
    }
}
