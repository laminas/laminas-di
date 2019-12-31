<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\Definition;

use Laminas\Di\Definition\RuntimeDefinition;
use PHPUnit_Framework_TestCase as TestCase;

class RuntimeDefinitionTest extends TestCase
{
    /**
     * @group Laminas-308
     */
    public function testStaticMethodsNotIncludedInDefinitions()
    {
        $definition = new RuntimeDefinition;
        $this->assertTrue($definition->hasMethod('LaminasTest\Di\TestAsset\SetterInjection\StaticSetter', 'setFoo'));
        $this->assertFalse($definition->hasMethod('LaminasTest\Di\TestAsset\SetterInjection\StaticSetter', 'setName'));
    }

    public function testIncludesDefaultMethodParameters()
    {
        $definition = new RuntimeDefinition();

        $definition->forceLoadClass('LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters');

        $this->assertSame(
            array(
                'LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:0' => array(
                    'a',
                    null,
                    false,
                    null,
                ),
                'LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:1' => array(
                    'b',
                    null,
                    false,
                    'defaultConstruct',
                ),
                'LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters::__construct:2' => array(
                    'c',
                    null,
                    false,
                    array(),
                ),
            ),
            $definition->getMethodParameters(
                'LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters',
                '__construct'
            )
        );
    }

    public function testExceptionDefaultValue()
    {
        $definition = new RuntimeDefinition();

        $definition->forceLoadClass('RecursiveIteratorIterator');

        $this->assertSame(
            array(
                'RecursiveIteratorIterator::__construct:0' => array(
                    'iterator',
                    'Traversable',
                    true,
                    null,
                ),
                'RecursiveIteratorIterator::__construct:1' => Array (
                    'mode',
                    null,
                    true,
                    null,
                ),
                'RecursiveIteratorIterator::__construct:2' => Array (
                    'flags',
                    null,
                    true,
                    null,
                ),
            ),
            $definition->getMethodParameters(
                'RecursiveIteratorIterator',
                '__construct'
            )
        );
    }
}
