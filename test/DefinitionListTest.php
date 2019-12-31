<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\Definition\ClassDefinition;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\DefinitionList;
use PHPUnit_Framework_TestCase as TestCase;

class DefinitionListTest extends TestCase
{
    public function testGetClassSupertypes()
    {
        $definitionClassA = new ClassDefinition("A");
        $superTypesA = array("superA");
        $definitionClassA->setSupertypes($superTypesA);

        $definitionClassB = new ClassDefinition("B");
        $definitionClassB->setSupertypes(array("superB"));

        $definitionList = new DefinitionList(array($definitionClassA, $definitionClassB));

        $this->assertEquals($superTypesA, $definitionList->getClassSupertypes("A"));

    }
}
