<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition;

use Laminas\Di\Definition\ClassDefinitionInterface;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Exception;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RuntimeDefinition::class)]
final class RuntimeDefinitionTest extends TestCase
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

    public function testSetExplicitClassesReplacesPreviousValues(): void
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

    /** @return non-empty-array<non-empty-string, array{class-string}> */
    public static function provideExistingClasses(): array
    {
        return [
            'A'             => [TestAsset\A::class],
            'B'             => [TestAsset\B::class],
            'NoConstructor' => [TestAsset\Constructor\NoConstructor::class],
        ];
    }

    public static function provideInvalidClasses(): array
    {
        return [
            'interface' => [TestAsset\DummyInterface::class],
            'badname'   => ['No\\Such\\Class.Because.Bad.Naming'],
        ];
    }

    /**
     */
    #[DataProvider('provideInvalidClasses')]
    public function testSetInvalidExplicitClassThrowsException(string $class)
    {
        $definition = new RuntimeDefinition();

        $this->expectException(Exception\ClassNotFoundException::class);
        $definition->setExplicitClasses([$class]);
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
     */
    #[DataProvider('provideInvalidClasses')]
    public function testAddInvalidExplicitClassThrowsException(string $class)
    {
        $definition = new RuntimeDefinition();

        $this->expectException(Exception\ClassNotFoundException::class);
        $definition->addExplicitClass($class);
    }

    #[DataProvider('provideExistingClasses')]
    public function testHasClassReturnsTrueDynamically(string $class): void
    {
        $this->assertTrue(
            (new RuntimeDefinition())->hasClass($class)
        );
    }

    /**
     */
    #[DataProvider('provideInvalidClasses')]
    public function testHasClassReturnsFalseForInvalidClasses(string $class)
    {
        $this->assertFalse(
            (new RuntimeDefinition())->hasClass($class)
        );
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideExistingClasses')]
    public function testGetClassDefinition(string $class): void
    {
        $definition = new RuntimeDefinition();
        $result     = $definition->getClassDefinition($class);

        $this->assertInstanceOf(ClassDefinitionInterface::class, $result);
        $this->assertInstanceOf(ReflectionClass::class, $result->getReflection());
        $this->assertSame($class, $result->getReflection()->name);
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideExistingClasses')]
    public function testGetClassDefinitionAutoPopulatesClass(string $class): void
    {
        $definition = new RuntimeDefinition();

        $this->assertSame([], $definition->getClasses());
        $definition->getClassDefinition($class);
        $this->assertEquals([$class], $definition->getClasses());
    }
}
