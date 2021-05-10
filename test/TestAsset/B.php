<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

class B
{
    /** @var A */
    public $injectedA;

    public function __construct(A $a)
    {
        $this->injectedA = $a;
    }
}
