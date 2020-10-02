<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

/**
 * FactoryGenerator test case.
 */
class InjectorGeneratorTest extends TestCase
{
    const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated';

    use GeneratorTestTrait;
    use ProphecyTrait;

    public function testGenerateCreatesFiles() : void
    {
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            TestAsset\RequiresA::class
        ]);

        $this->assertFileExists($this->dir . '/Factory/LaminasTest/Di/TestAsset/RequiresAFactory.php');
        $this->assertFileExists($this->dir . '/GeneratedInjector.php');
        $this->assertFileExists($this->dir . '/factories.php');
        $this->assertFileExists($this->dir . '/autoload.php');
    }

    public function testGeneratedInjectorClassCode() : void
    {
        // The namespace must be unique, Since we will attempt to load the
        // generated class
        $namespace = self::DEFAULT_NAMESPACE;
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $namespace);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([]);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/injector-class.php',
            $this->dir . '/GeneratedInjector.php'
        );
    }

    public function testGeneratedFactoryListCode() : void
    {
        // The namespace must be unique, Since we will attempt to load the
        // generated class
        $namespace = self::DEFAULT_NAMESPACE;
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $namespace);

        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            TestAsset\A::class,
            TestAsset\B::class
        ]);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/factories-file.php',
            $this->dir . '/factories.php'
        );
    }

    public function testSetCustomNamespace() : void
    {
        $expected = self::DEFAULT_NAMESPACE . uniqid();
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $expected);

        $this->assertEquals($expected, $generator->getNamespace());
    }

    public function testGeneratorLogsDebugForEachClass()
    {
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $logger = $this->prophesize(LoggerInterface::class);

        $generator = new InjectorGenerator($config, $resolver, null, $logger->reveal());
        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            TestAsset\B::class
        ]);

        $logger->debug(Argument::containingString(TestAsset\B::class))->shouldHaveBeenCalled();
    }

    public function testGeneratorLogsErrorWhenFactoryGenerationFailed()
    {
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $logger = $this->prophesize(LoggerInterface::class);
        $generator = new InjectorGenerator($config, $resolver, null, $logger->reveal());

        $generator->setOutputDirectory($this->dir);
        $generator->generate([
            'Bad.And.Undefined.ClassName'
        ]);

        $logger->error(Argument::containingString('Bad.And.Undefined.ClassName'))->shouldHaveBeenCalled();
    }
}
