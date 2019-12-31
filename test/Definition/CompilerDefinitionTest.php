<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\Definition;

use Laminas\Code\Scanner\DirectoryScanner;
use Laminas\Code\Scanner\FileScanner;
use Laminas\Di\Definition\CompilerDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class CompilerDefinitionTest extends TestCase
{
    public function testCompilerCompilesAgainstConstructorInjectionAssets()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/CompilerClasses');
        $definition->compile();

        $this->assertTrue($definition->hasClass('LaminasTest\Di\TestAsset\CompilerClasses\A'));

        $assertClasses = array(
            'LaminasTest\Di\TestAsset\CompilerClasses\A',
            'LaminasTest\Di\TestAsset\CompilerClasses\B',
            'LaminasTest\Di\TestAsset\CompilerClasses\C',
            'LaminasTest\Di\TestAsset\CompilerClasses\D',
        );
        $classes = $definition->getClasses();
        foreach ($assertClasses as $assertClass) {
            $this->assertContains($assertClass, $classes);
        }

        // @todo this needs to be resolved, not the short name
        // $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\D'));

        $this->assertEquals('__construct', $definition->getInstantiator('LaminasTest\Di\TestAsset\CompilerClasses\A'));
        $this->assertTrue($definition->hasMethods('LaminasTest\Di\TestAsset\CompilerClasses\C'));


        $this->assertArrayHasKey('setB', $definition->getMethods('LaminasTest\Di\TestAsset\CompilerClasses\C'));
        $this->assertTrue($definition->hasMethod('LaminasTest\Di\TestAsset\CompilerClasses\C', 'setB'));

        $this->assertEquals(
            array('LaminasTest\Di\TestAsset\CompilerClasses\C::setB:0' => array('b', 'LaminasTest\Di\TestAsset\CompilerClasses\B', true, null)),
            $definition->getMethodParameters('LaminasTest\Di\TestAsset\CompilerClasses\C', 'setB')
        );
    }

    public function testCompilerSupertypes()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/CompilerClasses');
        $definition->compile();
        $this->assertEquals(0, count($definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\C')));
        $this->assertEquals(1, count($definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\D')));
        $this->assertEquals(2, count($definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E')));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerDirectoryScannerAndFileScanner()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectoryScanner(new DirectoryScanner(__DIR__ . '/../TestAsset/CompilerClasses'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/A.php'));
        $definition->compile();
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerFileScanner()
    {
        $definition = new CompilerDefinition;
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/C.php'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/D.php'));
        $definition->addCodeScannerFile(new FileScanner(__DIR__ . '/../TestAsset/CompilerClasses/E.php'));
        $definition->compile();
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\D'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\C', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
        $this->assertContains('LaminasTest\Di\TestAsset\CompilerClasses\D', $definition->getClassSupertypes('LaminasTest\Di\TestAsset\CompilerClasses\E'));
    }

    public function testCompilerReflectionException()
    {
        $this->setExpectedException('ReflectionException', 'Class LaminasTest\Di\TestAsset\InvalidCompilerClasses\Foo does not exist');
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/InvalidCompilerClasses');
        $definition->compile();
    }

    public function testCompilerAllowReflectionException()
    {
        $definition = new CompilerDefinition;
        $definition->setAllowReflectionExceptions();
        $definition->addDirectory(__DIR__ . '/../TestAsset/InvalidCompilerClasses');
        $definition->compile();
        $parameters = $definition->getMethodParameters('LaminasTest\Di\TestAsset\InvalidCompilerClasses\InvalidClass', '__construct');

        // The exception gets caught before the parameter's class is set
        $this->assertCount(1, current($parameters));
    }

    /**
     * @group Laminas-308
     */
    public function testStaticMethodsNotIncludedInDefinitions()
    {
        $definition = new CompilerDefinition;
        $definition->addDirectory(__DIR__ . '/../TestAsset/SetterInjection');
        $definition->compile();
        $this->assertTrue($definition->hasMethod('LaminasTest\Di\TestAsset\SetterInjection\StaticSetter', 'setFoo'));
        $this->assertFalse($definition->hasMethod('LaminasTest\Di\TestAsset\SetterInjection\StaticSetter', 'setName'));
    }
}
