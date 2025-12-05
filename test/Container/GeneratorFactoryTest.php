<?php

declare(strict_types=1);

namespace LaminasTest\Di\Container;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Container\GeneratorFactory;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Injector;
use Laminas\Di\Resolver\DependencyResolver;
use Laminas\ServiceManager\ServiceManager;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;

use function uniqid;

#[CoversClass(GeneratorFactory::class)]
final class GeneratorFactoryTest extends TestCase
{
    public function testInvokeCreatesGenerator(): void
    {
        $injector = new Injector();
        $factory  = new GeneratorFactory();

        $result = $factory->create($injector->getContainer());
        $this->assertInstanceOf(InjectorGenerator::class, $result);
    }

    public function testFactoryUsesDiConfigContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnCallback(static fn($type): bool => $type === ConfigInterface::class);

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn(new Config());

        $factory = new GeneratorFactory();
        $factory->create($container);
    }

    public function testSetsOutputDirectoryFromConfig(): void
    {
        $vfs       = vfsStream::setup(uniqid('laminas-di'));
        $expected  = $vfs->url();
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

    public function testSetsNamespaceFromConfig(): void
    {
        $expected  = 'LaminasTest\\Di\\' . uniqid('Generated');
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

    public function testDefaultLogger(): void
    {
        $generator  = (new GeneratorFactory())->create(new ServiceManager());
        $reflection = new ReflectionClass($generator);
        $property   = $reflection->getProperty('logger');

        $this->assertInstanceOf(NullLogger::class, $property->getValue($generator));
    }

    public function testSetsLoggerFromConfig(): void
    {
        $logger    = $this->createMock(LoggerInterface::class);
        $container = new ServiceManager();
        $container->setService('MyCustomLogger', $logger);
        $container->setService('config', [
            'dependencies' => [
                'auto' => [
                    'aot' => [
                        'logger' => 'MyCustomLogger',
                    ],
                ],
            ],
        ]);

        $generator  = (new GeneratorFactory())->create($container);
        $reflection = new ReflectionClass($generator);
        $property   = $reflection->getProperty('logger');

        $this->assertNotInstanceOf(NullLogger::class, $property->getValue($generator));
    }

    public function testInvokeCallsCreate(): void
    {
        $mock = $this->createPartialMock(GeneratorFactory::class, ['create']);

        $container = $this->createMock(ContainerInterface::class);

        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, uniqid('Test'));

        $mock->expects($this->once())
            ->method('create')
            ->with($container)
            ->willReturn($generator);

        $result = $mock($container);
        $this->assertInstanceOf(InjectorGenerator::class, $result);
    }
}
