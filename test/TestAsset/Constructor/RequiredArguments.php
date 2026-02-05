<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

use ArrayAccess;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class RequiredArguments
{
    /** @param mixed $anyDep */
    public function __construct(NoConstructor $objectDep, ArrayAccess $internalClassDep, $anyDep)
    {
    }
}
