<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

class Php7
{
    public function __construct(string $stringDep, int $numDep, callable $callbacDep)
    {
    }
}
