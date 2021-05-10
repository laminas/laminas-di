<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset;

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
