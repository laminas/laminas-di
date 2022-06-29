<?php

declare(strict_types=1);

namespace LaminasBench\Di;

use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithMultipleConstructorParameters;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructor;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructorParameters;

class ClassDefinitionBench
{
    private ClassDefinition $noConstructor;
    private ClassDefinition $noConstructorParameters;
    private ClassDefinition $multipleConstructorParameters;

    public function __construct()
    {
        $this->noConstructor = new ClassDefinition(ClassDefinitionWithoutConstructor::class);
        $this->noConstructorParameters = new ClassDefinition(ClassDefinitionWithoutConstructorParameters::class);
        $this->multipleConstructorParameters = new ClassDefinition(ClassDefinitionWithMultipleConstructorParameters::class);
    }

    public function benchGetParametersBeforeTransparentCachingNoConstructor(): void
    {
        $noConstructor = clone $this->noConstructor;

        $noConstructor->getParameters();
    }

    public function benchGetParametersBeforeTransparentCachingNoConstructorParameters(): void
    {
        $noConstructorParameters = clone $this->noConstructorParameters;

        $noConstructorParameters->getParameters();
    }

    public function benchGetParametersBeforeTransparentCachingMultipleConstructorParameters(): void
    {
        $multipleConstructorParameters = clone $this->multipleConstructorParameters;

        $multipleConstructorParameters->getParameters();
    }
}
