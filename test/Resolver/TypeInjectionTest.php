<?php

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

use function sprintf;
use function uniqid;

/**
 * @covers \Laminas\Di\Resolver\TypeInjection
 */
class TypeInjectionTest extends TestCase
{
    public function testImplementsContract()
    {
        $this->assertInstanceOf(InjectionInterface::class, new TypeInjection('typename'));
    }

    public function testToValueUsesContainer()
    {
        $container     = $this->createMock(ContainerInterface::class);
        $typename      = uniqid('TypeName');
        $expectedValue = new stdClass();
        $subject       = new TypeInjection($typename);

        $container
            ->method('get')
            ->with($typename)
            ->willReturn($expectedValue);

        $this->assertSame($expectedValue, $subject->toValue($container));
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
    public function testIsExportableIsAlwaysTrue(string $typeName)
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
