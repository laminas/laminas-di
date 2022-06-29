<?php

declare(strict_types=1);

namespace LaminasBench\Di\BenchAsset;

class ClassDefinitionWithMultipleConstructorParameters
{
    private string $first;
    private int $second;
    /** @var mixed */
    private $third;

    /** @param mixed $third */
    public function __construct(
        string $first,
        int $second,
        $third
    ) {
        $this->first  = $first;
        $this->second = $second;
        $this->third  = $third;
    }
}
