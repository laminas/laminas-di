<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition\Reflection;

use LaminasTest\Di\TestAsset;
use ReflectionClass;

use function substr;

trait ParameterTestTrait
{
    /** @param non-empty-string $methodName */
    private function reflectAsset(string $methodName, int $parameterIndex = 0): object
    {
        $all = (new ReflectionClass(TestAsset\Parameters::class))->getMethod($methodName)->getParameters();
        return $all[$parameterIndex];
    }

    private function buildReflectionArgsFromClass(string $classname): array
    {
        $class          = new ReflectionClass($classname);
        $invocationArgs = [];

        foreach ($class->getMethods() as $method) {
            $params                    = $method->getParameters();
            $typename                  = substr($method->name, 0, -4);
            $invocationArgs[$typename] = [$params[0], $typename];
        }

        return $invocationArgs;
    }

    public function provideBuiltinTypehintedReflections(): array
    {
        return $this->buildReflectionArgsFromClass(TestAsset\BuiltinTypehintParameters::class);
    }

    public function provideTypehintedParameterReflections(): array
    {
        return [
            'required' => [$this->reflectAsset('typehintRequired'), TestAsset\A::class],
            'optional' => [$this->reflectAsset('typehintOptional'), TestAsset\A::class],
        ];
    }

    public function provideTypelessParameterReflections(): array
    {
        return [
            'required' => [$this->reflectAsset('typelessRequired')],
            'optional' => [$this->reflectAsset('typelessOptional')],
        ];
    }
}
