<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition\Reflection;

use LaminasTest\Di\TestAsset;
use ReflectionClass;

use function substr;

trait ParameterTestTrait
{
    /** @param non-empty-string $methodName */
    private static function reflectAsset(string $methodName, int $parameterIndex = 0): object
    {
        $all = (new ReflectionClass(TestAsset\Parameters::class))->getMethod($methodName)->getParameters();
        return $all[$parameterIndex];
    }

    private static function buildReflectionArgsFromClass(string $classname): array
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

    public static function provideBuiltinTypehintedReflections(): array
    {
        return self::buildReflectionArgsFromClass(TestAsset\BuiltinTypehintParameters::class);
    }

    public static function provideTypehintedParameterReflections(): array
    {
        return [
            'required' => [self::reflectAsset('typehintRequired'), TestAsset\A::class],
            'optional' => [self::reflectAsset('typehintOptional'), TestAsset\A::class],
        ];
    }

    public static function provideTypelessParameterReflections(): array
    {
        return [
            'required' => [self::reflectAsset('typelessRequired')],
            'optional' => [self::reflectAsset('typelessOptional')],
        ];
    }
}
