<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use stdClass;

use function uniqid;

/**
 * @covers \Laminas\Di\Resolver\TypeInjection
 */
class TypeInjectionTest extends TestCase
{
    use ProphecyTrait;

    public function testImplementsContract()
    {
        $this->assertInstanceOf(InjectionInterface::class, new TypeInjection('typename'));
    }

    public function testToValueUsesContainer()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $typename = uniqid('TypeName');
        $expectedValue = new stdClass();
        $subject = new TypeInjection($typename);

        $container->get($typename)
            ->shouldBeCalled()
            ->willReturn($expectedValue);

        $this->assertSame($expectedValue, $subject->toValue($container->reveal()));
    }

    public function testExport()
    {
        $typename = 'TypeName';
        $expected = sprintf("'%s'", $typename);

        $this->assertSame($expected, (new ValueInjection($typename))->export());
    }

    public function provideTypeNames(): iterable
    {
        return [
            'arbitary' => ['SomeArbitaryTypeName'],
        ];
    }

    /**
     * @dataProvider provideTypeNames
     */
    public function testIsExportableIsAlwaysTrue($typeName)
    {
        $this->assertTrue((new TypeInjection($typeName))->isExportable());
    }

    public function testGetTypeIsDeprectaed()
    {
        $subject = new TypeInjection('SomeType');
        $this->expectDeprecation();
        $this->assertSame('SomeType', $subject->getType());
    }
}
