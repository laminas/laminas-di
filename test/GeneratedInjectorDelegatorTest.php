<?php

declare(strict_types=1);

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
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $container
            ->method('get')
            ->with('config')
            ->willReturn(['dependencies' => ['auto' => ['aot' => ['namespace' => []]]]]);

        $delegator = new GeneratedInjectorDelegator();

        self::assertInstanceOf(ContainerExceptionInterface::class, new InvalidServiceConfigException());
        $this->expectException(InvalidServiceConfigException::class);
        $this->expectExceptionMessage('namespace');
        $delegator($container, 'AnyString', function () {
        });
    }

    public function testGeneratedInjectorDoesNotExist()
    {
        $injector = $this->createMock(InjectorInterface::class);
        $callback = fn() => $injector;

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $delegator = new GeneratedInjectorDelegator();
        $result    = $delegator($container, $injector::class, $callback);
        $this->assertSame($result, $injector);
    }

    public function testGeneratedInjectorExists()
    {
        $injector = $this->createMock(InjectorInterface::class);
        $callback = fn() => $injector;

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container
            ->method('get')
            ->with('config')
            ->willReturn(['dependencies' => ['auto' => ['aot' => ['namespace' => 'LaminasTest\Di\TestAsset']]]]);

        $delegator = new GeneratedInjectorDelegator();
        $result    = $delegator($container, $injector::class, $callback);

        $this->assertInstanceOf(TestAsset\GeneratedInjector::class, $result);
        $this->assertSame($injector, $result->getInjector());
    }
}
