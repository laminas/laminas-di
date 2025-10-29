<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class TypelessDependency
{
    /** @var mixed */
    public $result;

    /** @param mixed $value */
    public function __construct($value)
    {
        $this->result = $value;
    }
}
