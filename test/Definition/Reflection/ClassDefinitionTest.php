<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition\Reflection;

use Laminas\Di\Definition\ParameterInterface;
use Laminas\Di\Definition\Reflection\ClassDefinition;
use LaminasTest\Di\TestAsset\ClaimTestClass;
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
class ClassDefinitionTest extends TestCase
{
    public function testGetReflection()
    {
        $result = (new ClassDefinition(HierarchyAsset\A::class))->getReflection();

        $this->assertInstanceOf(ReflectionClass::class, $result);
        $this->assertEquals(HierarchyAsset\A::class, $result->getName());
    }

    public function testGetSupertypesReturnsAllClasses()
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

    public function testGetSupertypesReturnsEmptyArray()
    {
        $supertypes = (new ClassDefinition(HierarchyAsset\A::class))->getSupertypes();

        $this->assertIsArray($supertypes);
        $this->assertEmpty($supertypes);
    }

    /**
     * Tests ClassDefinition->getInterfaces()
     */
    public function testGetInterfacesReturnsAllInterfaces()
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
    public function testGetInterfacesReturnsArray()
    {
        $result = (new ClassDefinition(HierarchyAsset\A::class))->getInterfaces();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function provideClassesWithParameters(): array
    {
        return [
            'optional' => [ConstructorAsset\OptionalArguments::class, 2],
            'required' => [ConstructorAsset\RequiredArguments::class, 3],
        ];
    }

    /**
     * @dataProvider provideClassesWithParameters
     */
    public function testGetParametersReturnsAllParameters(string $class, int $expectedItemCount)
    {
        $result = (new ClassDefinition($class))->getParameters();

        $this->assertIsArray($result);
        $this->assertCount($expectedItemCount, $result);
        $this->assertContainsOnlyInstancesOf(ParameterInterface::class, $result);
    }

    public function testGetParametersWithScalarTypehints()
    {
        $result = (new ClassDefinition(ConstructorAsset\Php7::class))->getParameters();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(ParameterInterface::class, $result);
    }

    public function provideParameterlessClasses(): array
    {
        return [
            'noargs'      => [ConstructorAsset\EmptyConstructor::class],
            'noconstruct' => [ConstructorAsset\NoConstructor::class],
        ];
    }

    /**
     * @dataProvider provideParameterlessClasses
     */
    public function testGetParametersReturnsAnArray(string $class)
    {
        $result = (new ClassDefinition($class))->getParameters();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

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
