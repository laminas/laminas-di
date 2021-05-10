<?php

namespace LaminasTest\Di;

use Laminas\Di\Exception\InvalidServiceConfigException;
use Laminas\Di\GeneratedInjectorDelegator;
use Laminas\Di\InjectorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function get_class;

class GeneratedInjectorDelegatorTest extends TestCase
{
    use ProphecyTrait;

    public function testProvidedNamespaceIsNotAString()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')
            ->willReturn(true)
            ->shouldBeCalledTimes(1);
        $container->get('config')
            ->willReturn(['dependencies' => ['auto' => ['aot' => ['namespace' => []]]]])
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
        $result    = $delegator($container->reveal(), get_class($injector), $callback);

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
        $result    = $delegator($container->reveal(), get_class($injector), $callback);

        $this->assertInstanceOf(TestAsset\GeneratedInjector::class, $result);
        $this->assertSame($injector, $result->getInjector());
    }
}
