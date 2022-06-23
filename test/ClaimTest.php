<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use LaminasTest\Di\TestAsset\ClaimTestClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function array_values;
use function uasort;

final class ClaimTest extends TestCase
{
    public function testRedundantUaSortInClassDefinition(): void
    {
        $reflectionClass       = new ReflectionClass(ClaimTestClass::class);
        $constructor           = $reflectionClass->getConstructor();
        $constructorParameters = $constructor->getParameters();

        $parameters = [];
        foreach ($constructorParameters as $parameter) {
            $parameters[$parameter->getName()] = $parameter;
        }

        static::assertEquals(
            $constructorParameters,
            array_values($parameters)
        );

        uasort(
            $parameters,
            fn ($a, $b) => $a->getPosition() - $b->getPosition()
        );

        static::assertEquals(
            $constructorParameters,
            array_values($parameters)
        );
    }
}
