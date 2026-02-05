<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class Php7
{
    public function __construct(string $stringDep, int $numDep, callable $callbacDep)
    {
    }
}
