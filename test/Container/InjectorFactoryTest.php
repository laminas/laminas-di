<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\Container;

use Laminas\Di\ConfigInterface;
use Laminas\Di\Container\InjectorFactory;
use Laminas\Di\InjectorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionObject;

/**
 * @coversDefaultClass Laminas\Di\Container\InjectorFactory
 */
class InjectorFactoryTest extends TestCase
{
    public function testFactoryIsInvokable()
    {
        $this->assertIsCallable(new InjectorFactory());
    }

    public function testCreateWillReturnAnInjectorInstance()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $result    = (new InjectorFactory())->create($container);

        $this->assertInstanceOf(InjectorInterface::class, $result);
    }

    public function testInvokeWillReturnAnInjectorInstance()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $factory   = new InjectorFactory();
        $result    = $factory($container);

        $this->assertInstanceOf(InjectorInterface::class, $result);
    }

    public function testUsesConfigServiceFromContainer()
    {
        $container  = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMockForAbstractClass();
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

        $property->setAccessible(true);
        $this->assertSame($configMock, $property->getValue($injector));
    }
}
