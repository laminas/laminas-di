<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Pseudotypes;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class CallableImplementation
{
    public function __invoke()
    {
    }
}
