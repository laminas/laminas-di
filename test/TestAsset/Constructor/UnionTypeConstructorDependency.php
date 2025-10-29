<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

use Countable;
use stdClass;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class UnionTypeConstructorDependency
{
    public function __construct(private stdClass|Countable $someDependency)
    {
    }
}
