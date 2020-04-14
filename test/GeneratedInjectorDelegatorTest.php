<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\Exception\InvalidServiceConfigException;
use Laminas\Di\GeneratedInjectorDelegator;
use Laminas\Di\InjectorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class GeneratedInjectorDelegatorTest extends TestCase
{
    public function testProvidedNamespaceIsNotAString()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')
            ->willReturn(true)
            ->shouldBeCalledTimes(1);
        $container->get('config')
            ->willReturn(['dependencies' => ['auto' => ['aot' => ['namespace' => new \stdClass()]]]])
            ->shouldBeCalledTimes(1);

        $delegator = new GeneratedInjectorDelegator();

        self::assertInstanceOf(ContainerExceptionInterface::class, new InvalidServiceConfigException());
        $this->expectException(InvalidServiceConfigException::class);
        $this->expectExceptionMessage('namespace');
        $delegator($container->reveal(), 'AnyString', function () {
        });
    }

    public function testGeneratedInjectorDoesNotExist()
    {
        $injector = $this->prophesize(InjectorInterface::class)->reveal();
        $callback = function () use ($injector) {
            return $injector;
        };

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false)->shouldBeCalledTimes(1);

        $delegator = new GeneratedInjectorDelegator();
        $result = $delegator($container->reveal(), get_class($injector), $callback);

        $this->assertSame($result, $injector);
    }

    public function testGeneratedInjectorExists()
    {
        $injector = $this->prophesize(InjectorInterface::class)->reveal();
        $callback = function () use ($injector) {
            return $injector;
        };

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')
            ->willReturn(true)
            ->shouldBeCalledTimes(1);
        $container->get('config')
            ->willReturn(['dependencies' => ['auto' => ['aot' => ['namespace' => 'LaminasTest\Di\TestAsset']]]])
            ->shouldBeCalledTimes(1);

        $delegator = new GeneratedInjectorDelegator();
        $result = $delegator($container->reveal(), get_class($injector), $callback);

        $this->assertInstanceOf(TestAsset\GeneratedInjector::class, $result);
        $this->assertSame($injector, $result->getInjector());
    }
}
