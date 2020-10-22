<?php

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function uniqid;

/**
 * FactoryGenerator test case.
 */
class InjectorGeneratorTest extends TestCase
{
    use GeneratorTestTrait;

    private const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated';

    public function testGenerateCreatesFiles(): void
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            TestAsset\RequiresA::class,
        ]);

        $this->assertFileExists($this->dir . '/Factory/LaminasTest/Di/TestAsset/RequiresAFactory.php');
        $this->assertFileExists($this->dir . '/GeneratedInjector.php');
        $this->assertFileExists($this->dir . '/factories.php');
        $this->assertFileExists($this->dir . '/autoload.php');
    }

    public function testGeneratedInjectorClassCode(): void
    {
        // The namespace must be unique, Since we will attempt to load the
        // generated class
        $namespace = self::DEFAULT_NAMESPACE;
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $namespace);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([]);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/injector-class.php',
            $this->dir . '/GeneratedInjector.php'
        );
    }

    public function testGeneratedFactoryListCode(): void
    {
        // The namespace must be unique, Since we will attempt to load the
        // generated class
        $namespace = self::DEFAULT_NAMESPACE;
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $namespace);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            TestAsset\A::class,
            TestAsset\B::class,
        ]);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/factories-file.php',
            $this->dir . '/factories.php'
        );
    }

    public function testSetCustomNamespace(): void
    {
        $expected  = self::DEFAULT_NAMESPACE . uniqid();
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $expected);

        $this->assertEquals($expected, $generator->getNamespace());
    }

    public function testGeneratorLogsDebugForEachClass(): void
    {
        $config   = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $logger   = $this->createMock(LoggerInterface::class);

        $generator = new InjectorGenerator($config, $resolver, null, $logger);
        $generator->setOutputDirectory($this->dir);

        $logger
            ->expects(self::atLeastOnce())
            ->method('debug')
            ->with(self::stringContains(TestAsset\B::class));

        $generator->generate([
            TestAsset\B::class,
        ]);
    }

    public function testGeneratorLogsErrorWhenFactoryGenerationFailed(): void
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $logger    = $this->createStub(LoggerInterface::class);
        $generator = new InjectorGenerator($config, $resolver, null, $logger);

        $generator->setOutputDirectory($this->dir);

        $logger
            ->expects(self::atLeastOnce())
            ->method('error')
            ->with(self::stringContains('Bad.And.Undefined.ClassName'));

        $generator->generate([
            'Bad.And.Undefined.ClassName',
        ]);
    }
}
