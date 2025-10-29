<?php

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function uniqid;

use const E_USER_DEPRECATED;

/**
 * @covers \Laminas\Di\Resolver\TypeInjection
 * @final This class should not be extended and will be marked final in version 4.0
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
        $expectedMessage = 'Laminas\Di\Resolver\TypeInjection::getType is deprecated. Please migrate to __toString()';

        set_error_handler(function ($errno, $errstr) use ($expectedMessage) {
            if ($errno === E_USER_DEPRECATED) {
                $this->assertStringContainsString($expectedMessage, $errstr);
                return true;
            }
            return false;
        }, E_USER_DEPRECATED);

        try {
            $subject = new TypeInjection('SomeType');
            $subject->getType();
        } finally {
            restore_error_handler();
        }
    }
}
