<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

/**
 * Will be used to prophesize invocations
 */
interface InvokableInterface
{
    public function __invoke();
}
