<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\AutoloadGenerator;
use PHPUnit\Framework\TestCase;

/**
 * AutoloadGenerator test case.
 */
class AutoloadGeneratorTest extends TestCase
{
    const DEFAULT_NAMESPACE = 'LaminasTest\Di\Generated';

    use GeneratorTestTrait;

    public function testGenerateCreatesFiles()
    {
        $generator = new AutoloadGenerator('LaminasTest\Di\Generated');
        $generator->setOutputDirectory($this->dir);
        $classmap = [
            'FooClass' => 'FooClass.php',
            'Bar\\Class' => 'Bar/Class.php'
        ];

        $generator->generate($classmap);
        $this->assertFileExists($this->dir . '/Autoloader.php');
        $this->assertFileExists($this->dir . '/autoload.php');
    }
}
