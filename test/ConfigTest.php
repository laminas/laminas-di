<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\Config;
use Laminas\Di\Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass Laminas\Di\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @var array
     */
    private $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = include __DIR__ . '/_files/sample-config.php';
    }

    public function testGetConfiguredTypeName()
    {
        $config = new Config($this->fixture);
        $this->assertEquals(['Foo', 'Bar'], $config->getConfiguredTypeNames());
    }

    public function testIsAlias()
    {
        $config = new Config($this->fixture);
        $this->assertTrue($config->isAlias('Bar'));
        $this->assertFalse($config->isAlias('Foo'));
        $this->assertFalse($config->isAlias('DoesNotExist'));
    }

    public function testGetClassForAlias()
    {
        $config = new Config($this->fixture);
        $this->assertEquals('Foo', $config->getClassForAlias('Bar'));
        $this->assertNull($config->getClassForAlias('Foo'));
        $this->assertNull($config->getClassForAlias('DoesNotExist'));
    }

    public function testGetParameters()
    {
        $config = new Config($this->fixture);
        $this->assertEquals(['a' => '*'], $config->getParameters('Foo'));
        $this->assertEquals([], $config->getParameters('Bar'));
        $this->assertEquals([], $config->getParameters('A'));
        $this->assertEquals([], $config->getParameters('B'));
    }

    public function testGetTypePreference()
    {
        $config = new Config($this->fixture);
        $this->assertEquals('GlobalA', $config->getTypePreference('A'));
        $this->assertEquals('GlobalB', $config->getTypePreference('B'));
        $this->assertNull($config->getTypePreference('NotDefined'));

        $this->assertEquals('LocalA', $config->getTypePreference('A', 'Foo'));
        $this->assertNull($config->getTypePreference('B', 'Foo'));
        $this->assertNull($config->getTypePreference('NotDefined', 'Foo'));

        $this->assertEquals('LocalB', $config->getTypePreference('B', 'Bar'));
        $this->assertNull($config->getTypePreference('A', 'Bar'));
        $this->assertNull($config->getTypePreference('NotDefined', 'Bar'));

        $this->assertNull($config->getTypePreference('A', 'NotDefinedType'));
        $this->assertNull($config->getTypePreference('B', 'NotDefinedType'));
        $this->assertNull($config->getTypePreference('NotDefined', 'NotDefinedType'));
    }

    public function testConstructWithInvalidOptionsThrowsException()
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        new Config(new stdClass());
    }

    public function testSetParameters()
    {
        $instance = new Config();
        $expected = [
            'bar' => 'Baz'
        ];

        $this->assertEmpty($instance->getParameters('Foo'));
        $instance->setParameters('Foo', $expected);
        $this->assertEquals($expected, $instance->getParameters('Foo'));
    }

    public function testSetGlobalTypePreference()
    {
        $instance = new Config();
        $this->assertNull($instance->getTypePreference('Foo'));
        $instance->setTypePreference('Foo', 'Bar');
        $this->assertEquals('Bar', $instance->getTypePreference('Foo'));
    }

    public function testSetTypePreferenceForTypeContext()
    {
        $instance = new Config();
        $this->assertNull($instance->getTypePreference('Foo', 'Baz'));
        $instance->setTypePreference('Foo', 'Bar', 'Baz');
        $this->assertEquals('Bar', $instance->getTypePreference('Foo', 'Baz'));
    }

    public function provideValidClassNames()
    {
        return [
            'class' => [ TestAsset\A::class ],
            'interface' => [ TestAsset\DummyInterface::class ],
        ];
    }

    /**
     * @dataProvider provideValidClassNames
     */
    public function testSetAlias($className)
    {
        $instance = new Config();

        $this->assertFalse($instance->isAlias('Foo.Bar'));

        $instance->setAlias('Foo.Bar', $className);

        $this->assertTrue($instance->isAlias('Foo.Bar'));
        $this->assertEquals($className, $instance->getClassForAlias('Foo.Bar'));
    }

    public function provideInvalidClassNames()
    {
        return [
            'badname' => [ 'Bad.Class.Name.For.PHP' ],
        ];
    }

    /**
     * @dataProvider provideInvalidClassNames
     */
    public function testSetAliasThrowsExceptionForInvalidClass(string $invalidClass)
    {
        $this->expectException(Exception\ClassNotFoundException::class);
        (new Config())->setAlias(uniqid('Some.Alias'), $invalidClass);
    }
}
