<?php

declare(strict_types=1);

namespace LaminasBench\Di;

use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructor;

/**
 * @Revs(1000)
 * @Iterations(20)
 * @Warmup(2)
 */
class GetParametersClassDefinitionWithoutConstructorBench
{
    private ClassDefinition $cd;

    public function __construct()
    {
        $this->cd = new ClassDefinition(
            ClassDefinitionWithoutConstructor::class
        );
    }

    public function benchGetParametersBeforeTransparentCaching(): void
    {
        $cd = clone $this->cd;

        $cd->getParameters();
    }
}
