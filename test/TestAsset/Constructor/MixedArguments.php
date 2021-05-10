<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

class MixedArguments
{
    /** @param null|mixed $anyDep */
    public function __construct(NoConstructor $objectDep, $anyDep = null)
    {
    }
}
