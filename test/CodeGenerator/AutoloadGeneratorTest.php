<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\AutoloadGenerator;
use PHPUnit\Framework\TestCase;

/**
 * AutoloadGenerator test case.
 */
class AutoloadGeneratorTest extends TestCase
{
    use GeneratorTestTrait;
    
    private const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated';

    public function testGenerateCreatesFiles()
    {
        $classmap  = [];
        $generator = new AutoloadGenerator(self::DEFAULT_NAMESPACE);
        $generator->setOutputDirectory($this->dir);
        $generator->generate($classmap);

        $this->assertFileExists($this->dir . '/Autoloader.php');
        $this->assertFileExists($this->dir . '/autoload.php');
    }

    public function testGeneratedAutoloaderClass()
    {
        $generator = new AutoloadGenerator(self::DEFAULT_NAMESPACE);
        $generator->setOutputDirectory($this->dir);
        $classmap = [
            'FooClass'   => 'FooClass.php',
            'Bar\\Class' => 'Bar/Class.php',
        ];

        $generator->generate($classmap);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/autoloader-class.php',
            $this->dir . '/Autoloader.php'
        );
    }

    public function testGeneratedAutoloadFile()
    {
        $generator = new AutoloadGenerator(self::DEFAULT_NAMESPACE);
        $generator->setOutputDirectory($this->dir);
        $classmap = [
            'FooClass'   => 'FooClass.php',
            'Bar\\Class' => 'Bar/Class.php',
        ];

        $generator->generate($classmap);

        self::assertFileEquals(
            __DIR__ . '/../_files/expected-codegen-results/autoload-file.php',
            $this->dir . '/autoload.php'
        );
    }
}
