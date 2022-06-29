<?php

declare(strict_types=1);

namespace LaminasBench\Di;

use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithConstructorInherited;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithMultipleConstructorParameters;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithMultipleMethods;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithNoOtherMethods;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructor;
use LaminasBench\Di\BenchAsset\ClassDefinitionWithoutConstructorParameters;

class ClassDefinitionBench
{
    private ClassDefinition $noConstructor;
    private ClassDefinition $noConstructorParameters;
    private ClassDefinition $multipleConstructorParameters;

    private ClassDefinition $noOtherMethods;
    private ClassDefinition $multipleMethods;
    private ClassDefinition $inheritedConstructor;

    public function __construct()
    {
        $this->noConstructor                 = new ClassDefinition(ClassDefinitionWithoutConstructor::class);
        $this->noConstructorParameters       = new ClassDefinition(ClassDefinitionWithoutConstructorParameters::class);
        $this->multipleConstructorParameters = new ClassDefinition(ClassDefinitionWithMultipleConstructorParameters::class);

        $this->noOtherMethods       = new ClassDefinition(ClassDefinitionWithNoOtherMethods::class);
        $this->multipleMethods      = new ClassDefinition(ClassDefinitionWithMultipleMethods::class);
        $this->inheritedConstructor = new ClassDefinition(ClassDefinitionWithConstructorInherited::class);
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

    public function benchGetParametersConstructorNoOtherMethods(): void
    {
        $noOtherMethods = clone $this->noOtherMethods;

        $noOtherMethods->getParameters();
    }

    public function benchGetParametersConstructorMultipleMethods(): void
    {
        $multipleMethods = clone $this->multipleMethods;

        $multipleMethods->getParameters();
    }

    public function benchGetParametersConstructorInheritedNoOtherMethods(): void
    {
        $inheritedConstructor = clone $this->inheritedConstructor;

        $inheritedConstructor->getParameters();
    }
}
