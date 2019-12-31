<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\Config;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\InjectorInterface;
use Laminas\Di\Resolver\DependencyResolver;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * FactoryGenerator test case.
 */
class InjectorGeneratorTest extends TestCase
{
    const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated';

    use GeneratorTestTrait;

    public function testGenerateCreatesFiles()
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

    public function testGeneratedInjectorIsValidCode()
    {
        // The namespace must be unique, Since we will attempt to load the
        // generated class
        $namespace = self::DEFAULT_NAMESPACE . uniqid();
        $config = new Config();
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new InjectorGenerator($config, $resolver, $namespace);
        $class = $namespace . '\\GeneratedInjector';

        $generator->setOutputDirectory($this->dir);
        $generator->generate([]);

        $this->assertFalse(class_exists($class, false));
        include $this->dir . '/GeneratedInjector.php';
        $this->assertTrue(class_exists($class, false));
    }
}
