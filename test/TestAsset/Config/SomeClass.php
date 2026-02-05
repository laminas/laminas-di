<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Config;

use LaminasTest\Di\TestAsset\A;
use LaminasTest\Di\TestAsset\B;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class SomeClass
{
    public function __construct(A $a, B $b)
    {
    }
}
