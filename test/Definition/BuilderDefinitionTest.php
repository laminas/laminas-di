<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\Definition;

use Laminas\Di\Definition\Builder;
use Laminas\Di\Definition\BuilderDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class BuilderDefinitionTest extends TestCase
{
    public function testBuilderImplementsDefinition()
    {
        $builder = new BuilderDefinition();
        $this->assertInstanceOf('Laminas\Di\Definition\DefinitionInterface', $builder);
    }

    public function testBuilderCanBuildClassWithMethods()
    {
        $class = new Builder\PhpClass();
        $class->setName('Foo');
        $class->addSuperType('Parent');

        $injectionMethod = new Builder\InjectionMethod();
        $injectionMethod->setName('injectBar');
        $injectionMethod->addParameter('bar', 'Bar');

        $class->addInjectionMethod($injectionMethod);

        $definition = new BuilderDefinition();
        $definition->addClass($class);

        $this->assertTrue($definition->hasClass('Foo'));
        $this->assertEquals('__construct', $definition->getInstantiator('Foo'));
        $this->assertContains('Parent', $definition->getClassSupertypes('Foo'));
        $this->assertTrue($definition->hasMethods('Foo'));
        $this->assertTrue($definition->hasMethod('Foo', 'injectBar'));
        $this->assertContains('injectBar', $definition->getMethods('Foo'));
        $this->assertEquals(
            ['Foo::injectBar:0' => ['bar', 'Bar', true, null]],
            $definition->getMethodParameters('Foo', 'injectBar')
        );
    }

    public function testBuilderDefinitionHasMethodsThrowsRuntimeException()
    {
        $definition = new BuilderDefinition();

        $this->setExpectedException('Laminas\Di\Exception\RuntimeException');
        $definition->hasMethods('Foo');
    }

    public function testBuilderDefinitionHasMethods()
    {
        $class = new Builder\PhpClass();
        $class->setName('Foo');

        $definition = new BuilderDefinition();
        $definition->addClass($class);

        $this->assertFalse($definition->hasMethods('Foo'));
        $class->createInjectionMethod('injectBar');

        $this->assertTrue($definition->hasMethods('Foo'));
    }

    public function testBuilderCanBuildFromArray()
    {
        $ini = include __DIR__ . '/../_files/sample-definitions.php';
        $iniAsArray = $ini['section-b'];
        $definitionArray = $iniAsArray['di']['definitions'][1];
        unset($definitionArray['class']);

        $definition = new BuilderDefinition();
        $definition->createClassesFromArray($definitionArray);

        $this->assertTrue($definition->hasClass('My\DbAdapter'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\DbAdapter'));
        $this->assertEquals(
            [
                'My\DbAdapter::__construct:0' => ['username', null, true, null],
                'My\DbAdapter::__construct:1' => ['password', null, true, null],
            ],
            $definition->getMethodParameters('My\DbAdapter', '__construct')
        );

        $this->assertTrue($definition->hasClass('My\Mapper'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Mapper'));
        $this->assertEquals(
            ['My\Mapper::__construct:0' => ['dbAdapter', 'My\DbAdapter', true, null]],
            $definition->getMethodParameters('My\Mapper', '__construct')
        );

        $this->assertTrue($definition->hasClass('My\Repository'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Repository'));
        $this->assertEquals(
            ['My\Repository::__construct:0' => ['mapper', 'My\Mapper', true, null]],
            $definition->getMethodParameters('My\Repository', '__construct')
        );
    }

    public function testCanCreateClassFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $class = $builder->createClass('Foo');

        $this->assertTrue($builder->hasClass('Foo'));
    }

    public function testCanCreateInjectionMethodsAndPopulateFromFluentInterface()
    {
        $builder = new BuilderDefinition();
        $foo     = $builder->createClass('Foo');
        $foo->setName('Foo');
        $foo->createInjectionMethod('setBar')
            ->addParameter('bar', 'Bar');
        $foo->createInjectionMethod('setConfig')
            ->addParameter('config', null);

        $this->assertTrue($builder->hasClass('Foo'));
        $this->assertTrue($builder->hasMethod('Foo', 'setBar'));
        $this->assertTrue($builder->hasMethod('Foo', 'setConfig'));

        $this->assertEquals(
            ['Foo::setBar:0' => ['bar', 'Bar', true, null]],
            $builder->getMethodParameters('Foo', 'setBar')
        );
        $this->assertEquals(
            ['Foo::setConfig:0' => ['config', null, true, null]],
            $builder->getMethodParameters('Foo', 'setConfig')
        );
    }

    public function testBuilderCanSpecifyClassToUseWithCreateClass()
    {
        $builder = new BuilderDefinition();
        $this->assertEquals('Laminas\Di\Definition\Builder\PhpClass', $builder->getClassBuilder());

        $builder->setClassBuilder('Foo');
        $this->assertEquals('Foo', $builder->getClassBuilder());
    }

    public function testClassBuilderCanSpecifyClassToUseWhenCreatingInjectionMethods()
    {
        $builder = new BuilderDefinition();
        $class   = $builder->createClass('Foo');

        $this->assertEquals('Laminas\Di\Definition\Builder\InjectionMethod', $class->getMethodBuilder());

        $class->setMethodBuilder('Foo');
        $this->assertEquals('Foo', $class->getMethodBuilder());
    }
}
