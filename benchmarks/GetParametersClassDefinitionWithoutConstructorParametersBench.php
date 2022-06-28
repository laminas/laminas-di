<?php

declare(strict_types=1);

namespace LaminasBench\Di;

use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructorParameters;

/**
 * @Revs(1000)
 * @Iterations(20)
 * @Warmup(2)
 */
class GetParametersClassDefinitionWithoutConstructorParametersBench
{
    private ClassDefinition $cd;

    public function __construct()
    {
        $this->cd = new ClassDefinition(
            ClassDefinitionWithoutConstructorParameters::class
        );
    }

    public function benchGetParametersBeforeTransparentCaching(): void
    {
        $cd = clone $this->cd;

        $cd->getParameters();
    }
}
