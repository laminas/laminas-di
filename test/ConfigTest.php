<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use Laminas\Di\Config;
use Laminas\Di\Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

use function uniqid;

/**
 * @coversDefaultClass \Laminas\Di\Config
 */
class ConfigTest extends TestCase
{
    private array $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = include __DIR__ . '/_files/sample-config.php';
    }

    public function testGetConfiguredTypeName(): void
    {
        $config = new Config($this->fixture);
        $this->assertEquals([TestAsset\Config\SomeClass::class, 'SomeAlias'], $config->getConfiguredTypeNames());
    }

    public function testIsAlias(): void
    {
        $config = new Config($this->fixture);
        $this->assertTrue($config->isAlias('SomeAlias'));
        $this->assertFalse($config->isAlias(TestAsset\Config\SomeClass::class));
        $this->assertFalse($config->isAlias('DoesNotExist'));
    }

    public function testGetClassForAlias(): void
    {
        $config = new Config($this->fixture);
        $this->assertEquals(TestAsset\Config\SomeClass::class, $config->getClassForAlias('SomeAlias'));
        $this->assertNull($config->getClassForAlias(TestAsset\Config\SomeClass::class));
        $this->assertNull($config->getClassForAlias('DoesNotExist'));
    }

    public function testGetParameters(): void
    {
        $config = new Config($this->fixture);
        $this->assertEquals(['a' => '*'], $config->getParameters(TestAsset\Config\SomeClass::class));
        $this->assertEquals([], $config->getParameters('SomeAlias'));
        $this->assertEquals([], $config->getParameters(TestAsset\A::class));
        $this->assertEquals([], $config->getParameters(TestAsset\B::class));
    }

    public function testGetTypePreference(): void
    {
        $config = new Config($this->fixture);
        $this->assertEquals('GlobalA', $config->getTypePreference(TestAsset\A::class));
        $this->assertEquals('GlobalB', $config->getTypePreference(TestAsset\B::class));
        $this->assertNull($config->getTypePreference('NotDefined'));

        $this->assertEquals(
            'LocalA',
            $config->getTypePreference(TestAsset\A::class, TestAsset\Config\SomeClass::class)
        );

        $this->assertNull($config->getTypePreference(TestAsset\B::class, TestAsset\Config\SomeClass::class));
        $this->assertNull($config->getTypePreference('NotDefined', TestAsset\Config\SomeClass::class));

        $this->assertEquals('LocalB', $config->getTypePreference(TestAsset\B::class, 'SomeAlias'));
        $this->assertNull($config->getTypePreference(TestAsset\A::class, 'SomeAlias'));
        $this->assertNull($config->getTypePreference('NotDefined', 'SomeAlias'));

        $this->assertNull($config->getTypePreference(TestAsset\A::class, 'NotDefinedType'));
        $this->assertNull($config->getTypePreference(TestAsset\B::class, 'NotDefinedType'));
        $this->assertNull($config->getTypePreference('NotDefined', 'NotDefinedType'));
    }

    public function testConstructWithInvalidOptionsThrowsException(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);

        /** @psalm-suppress InvalidArgument Explicitly tests type checks - maybe this test can be removed */
        new Config(new stdClass());
    }

    public function testSetParameters(): void
    {
        $instance = new Config();
        $expected = [
            'someParam' => 'SomeOtherType',
        ];

        $this->assertEmpty($instance->getParameters(TestAsset\Config\SomeClass::class));
        $instance->setParameters(TestAsset\Config\SomeClass::class, $expected);
        $this->assertEquals($expected, $instance->getParameters(TestAsset\Config\SomeClass::class));
    }

    public function testSetGlobalTypePreference(): void
    {
        $instance = new Config();
        $this->assertNull($instance->getTypePreference(TestAsset\Config\SomeClass::class));
        $instance->setTypePreference(TestAsset\Config\SomeClass::class, 'SomeAlias');
        $this->assertEquals('SomeAlias', $instance->getTypePreference(TestAsset\Config\SomeClass::class));
    }

    public function testSetTypePreferenceForTypeContext(): void
    {
        $instance = new Config();
        $this->assertNull($instance->getTypePreference(TestAsset\Config\SomeClass::class, 'SomeOtherType'));
        $instance->setTypePreference(TestAsset\Config\SomeClass::class, 'SomeAlias', 'SomeOtherType');
        $this->assertEquals(
            'SomeAlias',
            $instance->getTypePreference(TestAsset\Config\SomeClass::class, 'SomeOtherType')
        );
    }

    /**
     * @return array<string, array{0: class-string}>
     */
    public function provideValidClassNames(): array
    {
        return [
            'class'     => [TestAsset\A::class],
            'interface' => [TestAsset\DummyInterface::class],
        ];
    }

    /**
     * @dataProvider provideValidClassNames
     */
    public function testSetAlias(string $className): void
    {
        $instance = new Config();

        $this->assertFalse($instance->isAlias('Foo.Bar'));

        $instance->setAlias('Foo.Bar', $className);

        $this->assertTrue($instance->isAlias('Foo.Bar'));
        $this->assertEquals($className, $instance->getClassForAlias('Foo.Bar'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function provideInvalidClassNames(): array
    {
        return [
            'badname' => ['Bad.Class.Name.For.PHP'],
        ];
    }

    /**
     * @dataProvider provideInvalidClassNames
     */
    public function testSetAliasThrowsExceptionForInvalidClass(string $invalidClass): void
    {
        $this->expectException(Exception\ClassNotFoundException::class);
        (new Config())->setAlias(uniqid('Some.Alias'), $invalidClass);
    }
}
