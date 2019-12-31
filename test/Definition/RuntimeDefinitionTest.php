<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\Definition;

use Laminas\Di\Definition\ClassDefinitionInterface;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Exception;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass Laminas\Di\Definition\RuntimeDefinition
 */
class RuntimeDefinitionTest extends TestCase
{
    public function testSetExplicitClasses()
    {
        $expected = [
            TestAsset\A::class,
            TestAsset\B::class,
        ];

        $definition = new RuntimeDefinition();
        $definition->setExplicitClasses($expected);

        $this->assertEquals($expected, $definition->getClasses());
    }

    public function testSetExplicitClassesViaConstructor()
    {
        $expected = [
            TestAsset\A::class,
            TestAsset\B::class,
        ];

        $definition = new RuntimeDefinition($expected);
        $this->assertEquals($expected, $definition->getClasses());
    }

    public function testSetExplicitClassesReplacesPrefiousValues()
    {
        $expected = [
            TestAsset\A::class,
            TestAsset\B::class,
        ];

        $definition = new RuntimeDefinition();
        $definition->setExplicitClasses([TestAsset\Parameters::class]);
        $definition->setExplicitClasses($expected);

        $this->assertEquals($expected, $definition->getClasses());
    }

    public function provideExistingClasses()
    {
        return [
            'A'             => [TestAsset\A::class],
            'B'             => [TestAsset\B::class],
            'NoConstructor' => [TestAsset\Constructor\NoConstructor::class],
        ];
    }

    public function provideInvalidClasses()
    {
        return [
            'interface' => [TestAsset\DummyInterface::class],
            'badname'   => ['No\\Such\\Class.Because.Bad.Naming'],
        ];
    }

    /**
     * @dataProvider provideInvalidClasses
     */
    public function testSetInvalidExplicitClassThrowsException($class)
    {
        $definition = new RuntimeDefinition();

        $this->expectException(Exception\ClassNotFoundException::class);
        $definition->setExplicitClasses([ $class ]);
    }

    /**
     * Tests RuntimeDefinition->addExplicitClass()
     */
    public function testAddExplicitClass()
    {
        $expected = [
            TestAsset\A::class,
            TestAsset\B::class,
        ];

        $definition = new RuntimeDefinition();
        $definition->setExplicitClasses([TestAsset\A::class]);
        $definition->addExplicitClass(TestAsset\B::class);

        $this->assertEquals($expected, $definition->getClasses());
    }

    /**
     * @dataProvider provideInvalidClasses
     */
    public function testAddInvalidExplicitClassThrowsException($class)
    {
        $definition = new RuntimeDefinition();

        $this->expectException(Exception\ClassNotFoundException::class);
        $definition->addExplicitClass($class);
    }

    /**
     * @dataProvider provideExistingClasses
     */
    public function testHasClassReturnsTrueDynamically($class)
    {
        $this->assertTrue(
            (new RuntimeDefinition())->hasClass($class)
        );
    }

    /**
     * @dataProvider provideInvalidClasses
     */
    public function testHasClassReturnsFalseForInvalidClasses($class)
    {
        $this->assertFalse(
            (new RuntimeDefinition())->hasClass($class)
        );
    }

    /**
     * @dataProvider provideExistingClasses
     */
    public function testGetClassDefinition($class)
    {
        $definition = new RuntimeDefinition();
        $result = $definition->getClassDefinition($class);

        $this->assertInstanceOf(ClassDefinitionInterface::class, $result);
        $this->assertInstanceOf(ReflectionClass::class, $result->getReflection());
        $this->assertSame($class, $result->getReflection()->name);
    }

    /**
     * @dataProvider provideExistingClasses
     */
    public function testGetClassDefinitionAutoPopulatesClass($class)
    {
        $definition = new RuntimeDefinition();

        $this->assertSame([], $definition->getClasses());
        $definition->getClassDefinition($class);
        $this->assertEquals([$class], $definition->getClasses());
    }
}
