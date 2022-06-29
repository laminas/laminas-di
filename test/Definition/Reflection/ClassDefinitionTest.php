<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition\Reflection;

use Laminas\Di\Definition\ParameterInterface;
use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasTest\Di\TestAsset\ClassDefinitionRedundantUaSortTestDependency;
use LaminasTest\Di\TestAsset\Constructor as ConstructorAsset;
use LaminasTest\Di\TestAsset\Hierarchy as HierarchyAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function array_values;
use function sort;
use function uasort;

/**
 * @coversDefaultClass Laminas\Di\Definition\Reflection\ClassDefinition
 */
final class ClassDefinitionTest extends TestCase
{
    public function testGetReflection(): void
    {
        $result = (new ClassDefinition(HierarchyAsset\A::class))->getReflection();

        $this->assertInstanceOf(ReflectionClass::class, $result);
        $this->assertEquals(HierarchyAsset\A::class, $result->getName());
    }

    public function testGetSupertypesReturnsAllClasses(): void
    {
        $supertypes = (new ClassDefinition(HierarchyAsset\C::class))->getSupertypes();
        $expected   = [
            HierarchyAsset\A::class,
            HierarchyAsset\B::class,
        ];

        $this->assertIsArray($supertypes);

        sort($expected);
        sort($supertypes);

        $this->assertEquals($expected, $supertypes);
    }

    public function testGetSupertypesReturnsEmptyArray(): void
    {
        $supertypes = (new ClassDefinition(HierarchyAsset\A::class))->getSupertypes();

        $this->assertIsArray($supertypes);
        $this->assertEmpty($supertypes);
    }

    /**
     * Tests ClassDefinition->getInterfaces()
     */
    public function testGetInterfacesReturnsAllInterfaces(): void
    {
        $result   = (new ClassDefinition(HierarchyAsset\C::class))->getInterfaces();
        $expected = [
            HierarchyAsset\InterfaceA::class,
            HierarchyAsset\InterfaceB::class,
            HierarchyAsset\InterfaceC::class,
        ];

        $this->assertIsArray($result);

        sort($result);
        sort($expected);

        $this->assertEquals($expected, $result);
    }

    /**
     * Tests ClassDefinition->getInterfaces()
     */
    public function testGetInterfacesReturnsArray(): void
    {
        $result = (new ClassDefinition(HierarchyAsset\A::class))->getInterfaces();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @return array<string, array{class-string, int}> */
    public function provideClassesWithParameters(): array
    {
        return [
            'optional' => [ConstructorAsset\OptionalArguments::class, 2],
            'required' => [ConstructorAsset\RequiredArguments::class, 3],
        ];
    }

    /**
     * @dataProvider provideClassesWithParameters
     * @param class-string $class
     */
    public function testGetParametersReturnsAllParameters(string $class, int $expectedItemCount): void
    {
        $result = (new ClassDefinition($class))->getParameters();

        $this->assertIsArray($result);
        $this->assertCount($expectedItemCount, $result);
        $this->assertContainsOnlyInstancesOf(ParameterInterface::class, $result);
    }

    public function testGetParametersWithScalarTypehints(): void
    {
        $result = (new ClassDefinition(ConstructorAsset\Php7::class))->getParameters();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(ParameterInterface::class, $result);
    }

    /** @return array<string, array<class-string>> */
    public function provideParameterlessClasses(): array
    {
        return [
            'noargs'      => [ConstructorAsset\EmptyConstructor::class],
            'noconstruct' => [ConstructorAsset\NoConstructor::class],
        ];
    }

    /**
     * @dataProvider provideParameterlessClasses
     * @param class-string $class
     */
    public function testGetParametersReturnsAnArray(string $class): void
    {
        $result = (new ClassDefinition($class))->getParameters();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testRedundantUaSortInClassDefinition(): void
    {
        $reflectionClass       = new ReflectionClass(ClassDefinitionRedundantUaSortTestDependency::class);
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
