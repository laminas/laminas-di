<?php

declare(strict_types=1);

namespace LaminasBench\Di;

use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithMultipleConstructorParameters;

/**
 * @Revs(1000)
 * @Iterations(20)
 * @Warmup(2)
 */
class GetParametersClassDefinitionWithMultipleConstructorParametersBench
{
    private ClassDefinition $cd;

    public function __construct()
    {
        $this->cd = new ClassDefinition(
            ClassDefinitionWithMultipleConstructorParameters::class
        );
    }

    public function benchGetParametersBeforeTransparentCaching(): void
    {
        $cd = clone $this->cd;

        $cd->getParameters();
    }
}
