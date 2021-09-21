<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

use Iterator;
use ReturnTypeWillChange;

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
