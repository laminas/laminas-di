<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\Container;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Container\GeneratorFactory;
use Laminas\Di\Injector;
use Laminas\ServiceManager\ServiceManager;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers Laminas\Di\Container\GeneratorFactory
 */
class GeneratorFactoryTest extends TestCase
{
    public function testInvokeCreatesGenerator() : void
    {
        $injector = new Injector();
        $factory = new GeneratorFactory();

        $result = $factory->create($injector->getContainer());
        $this->assertInstanceOf(InjectorGenerator::class, $result);
    }

    public function testFactoryUsesDiConfigContainer() : void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $container->method('has')->willReturnCallback(function ($type) {
            return $type == ConfigInterface::class;
        });

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn(new Config());

        $factory = new GeneratorFactory();
        $factory->create($container);
    }

    public function testSetsOutputDirectoryFromConfig() : void
    {
        $vfs = vfsStream::setup(uniqid('laminas-di'));
        $expected = $vfs->url();
        $container = new ServiceManager();
        $container->setService('config', [
            'dependencies' => [
                'auto' => [
                    'aot' => [
                        'directory' => $expected,
                    ],
                ],
            ],
        ]);

        $generator = (new GeneratorFactory())->create($container);
        $this->assertEquals($expected, $generator->getOutputDirectory());
    }

    public function testSetsNamespaceFromConfig() : void
    {
        $expected = 'LaminasTest\\Di\\' . uniqid('Generated');
        $container = new ServiceManager();
        $container->setService('config', [
            'dependencies' => [
                'auto' => [
                    'aot' => [
                        'namespace' => $expected,
                    ],
                ],
            ],
        ]);

        $generator = (new GeneratorFactory())->create($container);
        $this->assertEquals($expected, $generator->getNamespace());
    }

    public function testInvokeCallsCreate() : void
    {
        $mock = $this->getMockBuilder(GeneratorFactory::class)
            ->setMethods(['create'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMockForAbstractClass();

        $mock->expects($this->once())
            ->method('create')
            ->with($container);

        $result = $mock($container);
        $this->assertInstanceOf(InjectorGenerator::class, $result);
    }
}
