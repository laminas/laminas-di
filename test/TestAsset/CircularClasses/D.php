<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\CircularClasses;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class D
{
    public function __construct(E $e)
    {
    }
}
