<?php

declare(strict_types=1);

namespace LaminasTest\Di\Container\ServiceManager;

use Laminas\Di\Container\AutowireFactory as GenericAutowireFactory;
use Laminas\Di\Container\ServiceManager\AutowireFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * AutowireFactory test case.
 */
#[CoversClass(AutowireFactory::class)]
final class AutowireFactoryTest extends TestCase
{
    public function testInvokeIsPassedToGenericFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $mock      = $this->createMock(GenericAutowireFactory::class);
        $expected  = new stdClass();
        $className = 'AnyClassName';

        // Container must not be called directly
        $container->expects($this->never())->method('has');
        $container->expects($this->never())->method('get');

        $mock->expects($this->once())
            ->method('create')
            ->with($container, $className)
            ->willReturn($expected);

        $factory = new AutowireFactory($mock);

        $this->assertSame($expected, $factory($container, $className));
    }

    public function testCanCreateIsPassedToGenericFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $mock      = $this->createMock(GenericAutowireFactory::class);
        $className = 'AnyClassName';

        // Container must not be called directly
        $container->expects($this->never())->method('has');
        $container->expects($this->never())->method('get');

        $mock->expects($this->once())
            ->method('canCreate')
            ->with($container, $className)
            ->willReturn(true);

        $factory = new AutowireFactory($mock);

        $this->assertTrue($factory->canCreate($container, $className));
    }
}
