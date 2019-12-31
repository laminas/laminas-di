<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Di\Definition;

use Laminas\Di\Definition\ClassDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class ClassDefinitionTest extends TestCase
{
    public function testClassImplementsDefinition()
    {
        $definition = new ClassDefinition('Foo');
        $this->assertInstanceOf('Laminas\Di\Definition\DefinitionInterface', $definition);
    }

    public function testClassDefinitionHasMethods()
    {
        $definition = new ClassDefinition('Foo');
        $this->assertFalse($definition->hasMethods('Foo'));
        $definition->addMethod('doBar');
        $this->assertTrue($definition->hasMethods('Foo'));
    }
}
