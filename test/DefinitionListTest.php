<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\Definition\BuilderDefinition;
use Laminas\Di\Definition\ClassDefinition;
use Laminas\Di\DefinitionList;
use PHPUnit_Framework_TestCase as TestCase;

class DefinitionListTest extends TestCase
{
    public function testGetClassSupertypes()
    {
        $definitionClassA = new ClassDefinition("A");
        $superTypesA = ["superA"];
        $definitionClassA->setSupertypes($superTypesA);

        $definitionClassB = new ClassDefinition("B");
        $definitionClassB->setSupertypes(["superB"]);

        $definitionList = new DefinitionList([$definitionClassA, $definitionClassB]);

        $this->assertEquals($superTypesA, $definitionList->getClassSupertypes("A"));
    }

    public function testHasMethod()
    {
        $definitionClass = new ClassDefinition('foo');
        $definitionClass->addMethod('doFoo');
        $definitionList = new DefinitionList([$definitionClass]);

        $this->assertTrue($definitionList->hasMethod('foo', 'doFoo'));
        $this->assertFalse($definitionList->hasMethod('foo', 'doBar'));

        $definitionClass->addMethod('doBar');

        $this->assertTrue($definitionList->hasMethod('foo', 'doBar'));
    }

    public function testHasMethodAvoidAskingFromDefinitionsWhichDoNotIncludeClass()
    {
        $builderDefinition = new BuilderDefinition();

        $definitionClass = new ClassDefinition('foo');
        $definitionClass->addMethod('doFoo');

        $definitionList = new DefinitionList([$builderDefinition, $definitionClass]);

        $this->assertTrue($definitionList->hasMethod('foo', 'doFoo'));
    }
}
