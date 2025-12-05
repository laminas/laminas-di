<?php

declare(strict_types=1);

namespace LaminasTest\Di\Container;

use Laminas\Di\ConfigInterface;
use Laminas\Di\Container\InjectorFactory;
use Laminas\Di\InjectorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionObject;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InjectorFactory::class)]
final class InjectorFactoryTest extends TestCase
{
    public function testFactoryIsInvokable()
    {
        $this->assertIsCallable(new InjectorFactory());
    }

    public function testCreateWillReturnAnInjectorInstance()
    {
        $container = $this->createMock(ContainerInterface::class);
        $result    = (new InjectorFactory())->create($container);

        $this->assertInstanceOf(InjectorInterface::class, $result);
    }

    public function testInvokeWillReturnAnInjectorInstance()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new InjectorFactory();
        $result    = $factory($container);

        $this->assertInstanceOf(InjectorInterface::class, $result);
    }

    public function testUsesConfigServiceFromContainer()
    {
        $container  = $this->createMock(ContainerInterface::class);
        $configMock = $this->createMock(ConfigInterface::class);
        $container->expects($this->atLeastOnce())
            ->method('has')
            ->with(ConfigInterface::class)
            ->willReturn(true);

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($configMock);

        $injector = (new InjectorFactory())->create($container);

        $reflection = new ReflectionObject($injector);
        $property   = $reflection->getProperty('config');
        $this->assertSame($configMock, $property->getValue($injector));
    }
}
