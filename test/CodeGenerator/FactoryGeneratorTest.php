<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\FactoryGenerator;
use Laminas\Di\Config;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;

use function str_replace;

/**
 * FactoryGenerator test case.
 */
class FactoryGeneratorTest extends TestCase
{
    use GeneratorTestTrait;
    
    private const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated\Factory';

    public function testGenerateCreatesFiles()
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new FactoryGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir . '/Factory');
        $generator->generate(TestAsset\RequiresA::class);

        $this->assertFileExists($this->dir . '/Factory/LaminasTest/Di/TestAsset/RequiresAFactory.php');
    }

    public function testGenerateBuildsUpClassMap()
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new FactoryGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir . '/FactoryMultiple');

        $f1 = $generator->generate(TestAsset\RequiresA::class);
        $f2 = $generator->generate(TestAsset\Constructor\EmptyConstructor::class);

        $expected = [
            $f1 => str_replace('\\', '/', TestAsset\RequiresA::class) . 'Factory.php',
            $f2 => str_replace('\\', '/', TestAsset\Constructor\EmptyConstructor::class) . 'Factory.php',
        ];

        $this->assertEquals($expected, $generator->getClassmap());
    }

    public function testGenerateForClassWithoutParams()
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new FactoryGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir . '/Factory');
        $generator->generate(TestAsset\A::class);

        $this->assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/factories/without-params.php',
            $this->dir . '/Factory/LaminasTest/Di/TestAsset/AFactory.php'
        );
    }

    public function testGenerateForClassWithParams()
    {
        $config    = new Config();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), $config);
        $generator = new FactoryGenerator($config, $resolver, self::DEFAULT_NAMESPACE);

        $generator->setOutputDirectory($this->dir . '/Factory');
        $generator->generate(TestAsset\Constructor\MixedArguments::class);

        $this->assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/factories/with-params.php',
            $this->dir . '/Factory/LaminasTest/Di/TestAsset/Constructor/MixedArgumentsFactory.php'
        );
    }
}
