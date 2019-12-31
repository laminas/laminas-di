<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\Config;
use Laminas\Di\Definition;
use Laminas\Di\DefinitionList;
use Laminas\Di\Di;
use Laminas\Di\InstanceManager;

class DiTest extends \PHPUnit_Framework_TestCase
{

    public function testDiHasBuiltInImplementations()
    {
        $di = new Di();
        $this->assertInstanceOf('Laminas\Di\InstanceManager', $di->instanceManager());

        $definitions = $di->definitions();

        $this->assertInstanceOf('Laminas\Di\DefinitionList', $definitions);
        $this->assertInstanceOf('Laminas\Di\Definition\RuntimeDefinition', $definitions->top());
    }

    public function testDiConstructorCanTakeDependencies()
    {
        $dl = new DefinitionList(array());
        $im = new InstanceManager();
        $cg = new Config(array());
        $di = new Di($dl, $im, $cg);

        $this->assertSame($dl, $di->definitions());
        $this->assertSame($im, $di->instanceManager());

        $di->setDefinitionList($dl);
        $di->setInstanceManager($im);

        $this->assertSame($dl, $di->definitions());
        $this->assertSame($im, $di->instanceManager());
    }

    public function testGetRetrievesObjectWithMatchingClassDefinition()
    {
        $di = new Di();
        $obj = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj);
    }

    public function testGetRetrievesSameInstanceOnSubsequentCalls()
    {
        $config = new Config(array(
            'instance' => array(
                'LaminasTest\Di\TestAsset\BasicClass' => array(
                    'shared' => true,
                    ),
                ),
        ));
        $di = new Di(null, null, $config);
        $obj1 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $obj2 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertSame($obj1, $obj2);
    }

    public function testGetRetrievesDifferentInstanceOnSubsequentCallsIfSharingDisabled()
    {
        $config = new Config(array(
            'instance' => array(
                'LaminasTest\Di\TestAsset\BasicClass' => array(
                    'shared' => false,
                ),
            ),
        ));
        $di = new Di(null, null, $config);
        $obj1 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $obj2 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertNotSame($obj1, $obj2);
    }

    public function testGetRetrievesSameSharedInstanceOnUsingInConstructor()
    {
        $config = new Config(array(
            'instance' => array(
                'LaminasTest\Di\TestAsset\BasicClass' => array(
                    'shared' => true,
                ),
            ),
        ));
        $di = new Di(null, null, $config);
        $obj1 = $di->get('LaminasTest\Di\TestAsset\BasicClassWithParent', array('foo' => 0));
        $obj2 = $di->get('LaminasTest\Di\TestAsset\BasicClassWithParent', array('foo' => 1));
        $obj3 = $di->get('LaminasTest\Di\TestAsset\BasicClassWithParent', array('foo' => 2, 'non_exists' => 1));
        $objParent1 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $objParent2 = $di->get('LaminasTest\Di\TestAsset\BasicClass', array('foo' => 1));

        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClassWithParent', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClassWithParent', $obj2);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClassWithParent', $obj3);
        $this->assertSame($obj1->parent, $obj2->parent);
        $this->assertSame($obj2->parent, $obj3->parent);
        $this->assertSame($obj3->parent, $objParent1);
        $this->assertSame($obj3->parent, $objParent2);
    }

    public function testGetThrowsExceptionWhenUnknownClassIsUsed()
    {
        $di = new Di();

        $this->setExpectedException('Laminas\Di\Exception\ClassNotFoundException', 'could not be located in');
        $obj1 = $di->get('LaminasTest\Di\TestAsset\NonExistentClass');
    }

    public function testGetThrowsExceptionWhenMissingParametersAreEncountered()
    {
        $di = new Di();

        $this->setExpectedException('Laminas\Di\Exception\MissingPropertyException', 'Missing instance/object for ');
        $obj1 = $di->get('LaminasTest\Di\TestAsset\BasicClassWithParam');
    }

    public function testNewInstanceReturnsDifferentInstances()
    {
        $di = new Di();
        $obj1 = $di->newInstance('LaminasTest\Di\TestAsset\BasicClass');
        $obj2 = $di->newInstance('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertNotSame($obj1, $obj2);
    }

    public function testNewInstanceReturnsInstanceThatIsSharedWithGet()
    {
        $di = new Di();
        $obj1 = $di->newInstance('LaminasTest\Di\TestAsset\BasicClass');
        $obj2 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertSame($obj1, $obj2);
    }

    public function testNewInstanceReturnsInstanceThatIsNotSharedWithGet()
    {
        $di = new Di();
        $obj1 = $di->newInstance('LaminasTest\Di\TestAsset\BasicClass', array(), false);
        $obj2 = $di->get('LaminasTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertNotSame($obj1, $obj2);
    }

    public function testNewInstanceCanHandleClassesCreatedByCallback()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\CallbackClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->setInstantiator('LaminasTest\Di\TestAsset\CallbackClasses\A::factory');

        $di = new Di($definitionList);
        $a = $di->get('LaminasTest\Di\TestAsset\CallbackClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\CallbackClasses\A', $a);
    }

    public function testNewInstanceCanHandleComplexCallback()
    {
        $definitionList = new DefinitionList(array(
            $classdefB = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\CallbackClasses\B'),
            $classdefC = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\CallbackClasses\C'),
            new Definition\RuntimeDefinition()
        ));

        $classdefB->setInstantiator('LaminasTest\Di\TestAsset\CallbackClasses\B::factory');
        $classdefB->addMethod('factory', true);
        $classdefB->addMethodParameter('factory', 'c', array('type' => 'LaminasTest\Di\TestAsset\CallbackClasses\C', 'required' => true));
        $classdefB->addMethodParameter('factory', 'params', array('type' => 'Array', 'required'=>false));

        $di = new Di($definitionList);
        $b = $di->get('LaminasTest\Di\TestAsset\CallbackClasses\B', array('params'=>array('foo' => 'bar')));
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\CallbackClasses\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\CallbackClasses\C', $b->c);
        $this->assertEquals(array('foo' => 'bar'), $b->params);
    }


//    public function testCanSetInstantiatorToStaticFactory()
//    {
//        $config = new Config(array(
//            'definition' => array(
//                'class' => array(
//                    'LaminasTest\Di\TestAsset\DummyParams' => array(
//                        'instantiator' => array('LaminasTest\Di\TestAsset\StaticFactory', 'factory'),
//                    ),
//                    'LaminasTest\Di\TestAsset\StaticFactory' => array(
//                        'methods' => array(
//                            'factory' => array(
//                                'struct' => array(
//                                    'type' => 'LaminasTest\Di\TestAsset\Struct',
//                                    'required' => true,
//                                ),
//                                'params' => array(
//                                    'required' => true,
//                                ),
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            'instance' => array(
//                'LaminasTest\Di\TestAsset\DummyParams' => array(
//                    'parameters' => array(
//                        'struct' => 'LaminasTest\Di\TestAsset\Struct',
//                        'params' => array(
//                            'foo' => 'bar',
//                        ),
//                    ),
//                ),
//                'LaminasTest\Di\TestAsset\Struct' => array(
//                    'parameters' => array(
//                        'param1' => 'hello',
//                        'param2' => 'world',
//                    ),
//                ),
//            ),
//        ));
//        $di = new Di();
//        $di->configure($config);
//        $dummyParams = $di->get('LaminasTest\Di\TestAsset\DummyParams');
//        $this->assertEquals($dummyParams->params['param1'], 'hello');
//        $this->assertEquals($dummyParams->params['param2'], 'world');
//        $this->assertEquals($dummyParams->params['foo'], 'bar');
//        $this->assertArrayNotHasKey('methods', $di->definitions()->hasMethods('LaminasTest\Di\TestAsset\StaticFactory'));
//    }

    /**
     * @group ConstructorInjection
     */
    public function testGetWillResolveConstructorInjectionDependencies()
    {
        $di = new Di();
        $b = $di->get('LaminasTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\A', $b->a);
    }

    /**
     * @group ConstructorInjection
     */
    public function testGetWillResolveConstructorInjectionDependenciesAndInstanceAreTheSame()
    {
        $di = new Di();
        $b = $di->get('LaminasTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\A', $b->a);

        $b2 = $di->get('LaminasTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\B', $b2);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\A', $b2->a);

        $this->assertSame($b, $b2);
        $this->assertSame($b->a, $b2->a);
    }

    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependencies()
    {
        $di = new Di();
        $b = $di->newInstance('LaminasTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\A', $b->a);
    }

    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependenciesWithProperties()
    {
        $di = new Di();

        $im = $di->instanceManager();
        $im->setParameters('LaminasTest\Di\TestAsset\ConstructorInjection\X', array('one' => 1, 'two' => 2));

        $y = $di->newInstance('LaminasTest\Di\TestAsset\ConstructorInjection\Y');
        $this->assertEquals(1, $y->x->one);
        $this->assertEquals(2, $y->x->two);
    }

    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillThrowExceptionOnConstructorInjectionDependencyWithMissingParameter()
    {
        $di = new Di();

        $this->setExpectedException('Laminas\Di\Exception\MissingPropertyException', 'Missing instance/object for parameter');
        $b = $di->newInstance('LaminasTest\Di\TestAsset\ConstructorInjection\X');
    }

    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependenciesWithMoreThan2Dependencies()
    {
        $di = new Di();

        $im = $di->instanceManager();
        $im->setParameters('LaminasTest\Di\TestAsset\ConstructorInjection\X', array('one' => 1, 'two' => 2));

        $z = $di->newInstance('LaminasTest\Di\TestAsset\ConstructorInjection\Z');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\Y', $z->y);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\ConstructorInjection\X', $z->y->x);
    }

    /**
     * @group SetterInjection
     */
    public function testGetWillResolveSetterInjectionDependencies()
    {
        $di = new Di();
        // for setter injection, the dependency is not required, thus it must be forced
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\B',
            array('a' => new TestAsset\SetterInjection\A)
        );
        $b = $di->get('LaminasTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b->a);
    }

    /**
     * @group SetterInjection
     */
    public function testGetWillResolveSetterInjectionDependenciesAndInstanceAreTheSame()
    {
        $di = new Di();
        // for setter injection, the dependency is not required, thus it must be forced
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\B',
            array('a' => $a = new TestAsset\SetterInjection\A)
        );

        $b = $di->get('LaminasTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b->a);

        $b2 = $di->get('LaminasTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\B', $b2);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b2->a);

        $this->assertSame($b, $b2);
        $this->assertSame($b->a, $a);
        $this->assertSame($b2->a, $a);
    }

    /**
     * @group SetterInjection
     */
    public function testNewInstanceWillResolveSetterInjectionDependencies()
    {
        $di = new Di();
        // for setter injection, the dependency is not required, thus it must be forced
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\B',
            array('a' => new TestAsset\SetterInjection\A)
        );

        $b = $di->newInstance('LaminasTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b->a);
    }

    /**
     * @todo Setter Injections is not automatic, find a way to test this logically
     *
     * @group SetterInjection
     */
    public function testNewInstanceWillResolveSetterInjectionDependenciesWithProperties()
    {
        $di = new Di();

        $im = $di->instanceManager();
        $im->setParameters('LaminasTest\Di\TestAsset\SetterInjection\X', array('one' => 1, 'two' => 2));

        $x = $di->get('LaminasTest\Di\TestAsset\SetterInjection\X');
        $y = $di->newInstance('LaminasTest\Di\TestAsset\SetterInjection\Y', array('x' => $x));

        $this->assertEquals(1, $y->x->one);
        $this->assertEquals(2, $y->x->two);
    }

    /**
     * Test for Circular Dependencies (case 1)
     *
     * A->B, B->A
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionOnBasicCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException('Laminas\Di\Exception\CircularDependencyException');
        $di->newInstance('LaminasTest\Di\TestAsset\CircularClasses\A');
    }

    /**
     * Test for Circular Dependencies (case 2)
     *
     * C->D, D->E, E->C
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionOnThreeLevelCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException(
            'Laminas\Di\Exception\CircularDependencyException',
            'Circular dependency detected: LaminasTest\Di\TestAsset\CircularClasses\E depends on LaminasTest\Di\TestAsset\CircularClasses\C and viceversa'
        );
        $di->newInstance('LaminasTest\Di\TestAsset\CircularClasses\C');
    }

    /**
     * Test for Circular Dependencies (case 2)
     *
     * C->D, D->E, E->C
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionWhenEnteringInMiddleOfCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException(
            'Laminas\Di\Exception\CircularDependencyException',
            'Circular dependency detected: LaminasTest\Di\TestAsset\CircularClasses\C depends on LaminasTest\Di\TestAsset\CircularClasses\D and viceversa'
        );
        $di->newInstance('LaminasTest\Di\TestAsset\CircularClasses\D');
    }

    /**
     * Fix for PHP bug in is_subclass_of
     *
     * @see https://bugs.php.net/bug.php?id=53727
     */
    public function testNewInstanceWillUsePreferredClassForInterfaceHints()
    {
        $di = new Di();
        $di->instanceManager()->addTypePreference(
            'LaminasTest\Di\TestAsset\PreferredImplClasses\A',
            'LaminasTest\Di\TestAsset\PreferredImplClasses\BofA'
        );

        $c = $di->get('LaminasTest\Di\TestAsset\PreferredImplClasses\C');
        $a = $c->a;
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\PreferredImplClasses\BofA', $a);
        $d = $di->get('LaminasTest\Di\TestAsset\PreferredImplClasses\D');
        $this->assertSame($a, $d->a);
    }

    public function testInjectionInstancesCanBeInjectedMultipleTimes()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('addB');
        $classdef->addMethodParameter('addB', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));

        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'LaminasTest\Di\TestAsset\InjectionClasses\B'
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);

        $di = new Di($definitionList);
        $di->instanceManager()->addAlias('my-b1', 'LaminasTest\Di\TestAsset\InjectionClasses\B');
        $di->instanceManager()->addAlias('my-b2', 'LaminasTest\Di\TestAsset\InjectionClasses\B');

        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'my-b1',
                'my-b2'
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');

        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[1]);
        $this->assertNotSame(
            $a->bs[0],
            $a->bs[1]
        );
    }

    public function testInjectionCanHandleDisambiguationViaPositions()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('injectBOnce');
        $classdef->addMethod('injectBTwice');
        $classdef->addMethodParameter('injectBOnce', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));
        $classdef->addMethodParameter('injectBTwice', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));

        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'LaminasTest\Di\TestAsset\InjectionClasses\A::injectBOnce:0' => new \LaminasTest\Di\TestAsset\InjectionClasses\B('once'),
                'LaminasTest\Di\TestAsset\InjectionClasses\A::injectBTwice:0' => new \LaminasTest\Di\TestAsset\InjectionClasses\B('twice')
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('once', $a->bs[0]->id);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[1]);
        $this->assertEquals('twice', $a->bs[1]->id);
    }

    public function testInjectionCanHandleDisambiguationViaNames()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('injectBOnce');
        $classdef->addMethod('injectBTwice');
        $classdef->addMethodParameter('injectBOnce', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));
        $classdef->addMethodParameter('injectBTwice', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));

        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'LaminasTest\Di\TestAsset\InjectionClasses\A::injectBOnce:b' => new \LaminasTest\Di\TestAsset\InjectionClasses\B('once'),
                'LaminasTest\Di\TestAsset\InjectionClasses\A::injectBTwice:b' => new \LaminasTest\Di\TestAsset\InjectionClasses\B('twice')
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('once', $a->bs[0]->id);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[1]);
        $this->assertEquals('twice', $a->bs[1]->id);
    }

    public function testInjectionCanHandleMultipleInjectionsWithMultipleArguments()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('injectSplitDependency');
        $classdef->addMethodParameter('injectSplitDependency', 'b', array('required' => true, 'type' => 'LaminasTest\Di\TestAsset\InjectionClasses\B'));
        $classdef->addMethodParameter('injectSplitDependency', 'somestring', array('required' => true, 'type' => null));

        /**
         * First test that this works with a single call
         */
        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'injectSplitDependency' => array('b' => 'LaminasTest\Di\TestAsset\InjectionClasses\B', 'somestring' => 'bs-id')
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('bs-id', $a->bs[0]->id);

        /**
         * Next test that this works with multiple calls
         */
        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'LaminasTest\Di\TestAsset\InjectionClasses\A',
            array(
                'injectSplitDependency' => array(
                    array('b' => 'LaminasTest\Di\TestAsset\InjectionClasses\B', 'somestring' => 'bs-id'),
                    array('b' => 'LaminasTest\Di\TestAsset\InjectionClasses\C', 'somestring' => 'bs-id-for-c')
                )
            )
        );
        $a = $di->newInstance('LaminasTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('bs-id', $a->bs[0]->id);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\InjectionClasses\C', $a->bs[1]);
        $this->assertEquals('bs-id-for-c', $a->bs[1]->id);
    }

    /**
     * @group SetterInjection
     * @group SupertypeResolution
     */
    public function testInjectionForSetterInjectionWillConsultSupertypeDefinitions()
    {
        $di = new Di();
        // for setter injection, the dependency is not required, thus it must be forced
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\C',
            array('a' => new TestAsset\SetterInjection\A)
        );
        $c = $di->get('LaminasTest\Di\TestAsset\SetterInjection\C');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\C', $c);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $c->a);
    }

    /**
     * @group SetterInjection
     * @group SupertypeResolution
     */
    public function testInjectionForSetterInjectionWillConsultSupertypeDefinitionInClassDefinition()
    {
        $di = new Di();

        // for setter injection, the dependency is not required, thus it must be forced
        $classDef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\SetterInjection\B');
        $classDef->addMethod('setA', true);
        $di->definitions()->addDefinition($classDef, false); // top of stack b/c Runtime is already there

        $c = $di->get('LaminasTest\Di\TestAsset\SetterInjection\C');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\C', $c);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $c->a);
    }

    /**
     * @group SharedInstance
     */
    public function testMarkingClassAsNotSharedInjectsNewInstanceIntoAllRequestersButDependentsAreShared()
    {
        $di = new Di();
        $di->configure(new Config(array(
            'instance' => array(
                'LaminasTest\Di\TestAsset\SharedInstance\Lister' => array(
                    'shared' => false
                )
            )
        )));
        $movie = $di->get('LaminasTest\Di\TestAsset\SharedInstance\Movie');
        $venue = $di->get('LaminasTest\Di\TestAsset\SharedInstance\Venue');

        $this->assertNotSame($movie->lister, $venue->lister);
        $this->assertSame($movie->lister->sharedLister, $venue->lister->sharedLister);
    }

    public function testDiWillInjectDependenciesForInstance()
    {
        $di = new Di;

        // for setter injection, the dependency is not required, thus it must be forced
        $classDef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\SetterInjection\B');
        $classDef->addMethod('setA', true);
        $di->definitions()->addDefinition($classDef, false); // top of stack b/c Runtime is already there

        $b = new TestAsset\SetterInjection\B;
        $di->injectDependencies($b);
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b->a);
    }

    public function testDiWillInjectDependenciesForAlias()
    {
        $di = new Di;

        // for setter injection, the dependency is not required, thus it must be forced
        $classDef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\SetterInjection\B');
        $classDef->addMethod('setA', false);
        $classDef->addMethodParameter('setA', 'a', array('type' => 'LaminasTest\Di\TestAsset\SetterInjection\A', 'required' => false));
        $di->definitions()->addDefinition($classDef, false);
        $di->instanceManager()->addAlias('b_alias', 'LaminasTest\Di\TestAsset\SetterInjection\B');
        $di->instanceManager()->setInjections('b_alias', array('LaminasTest\Di\TestAsset\SetterInjection\A'));

        $b = $di->get('b_alias');
        $this->assertInstanceOf('LaminasTest\Di\TestAsset\SetterInjection\A', $b->a);
    }

    /*
     * @group SetterInjection
     * @group SupertypeResolution
     */
    public function testInjectionForSetterInjectionWillNotUseSupertypeWhenChildParamIsExplicitlyDefined()
    {
        $di = new Di();
        // for setter injection, the dependency is not required, thus it must be forced
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\InheritanceClasses\B',
            array('test' => 'b')
        );
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\InheritanceClasses\A',
            array('test' => 'a')
        );

        $b = $di->get('LaminasTest\Di\TestAsset\InheritanceClasses\B');
        $this->assertEquals('b', $b->test);

        $c = $di->get('LaminasTest\Di\TestAsset\InheritanceClasses\C');
        $this->assertEquals('b', $c->test);
    }

    /**
     * @group Laminas-260
     */
    public function testDiWillInjectClassNameAsStringAtCallTime()
    {
        $di = new Di;

        $classDef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\SetterInjection\D');
        $classDef->addMethod('setA', true);
        $classDef->addMethodParameter('setA', 'a', array('type' => false, 'required' => true));
        $di->definitions()->addDefinition($classDef, false);

        $d = $di->get(
            'LaminasTest\Di\TestAsset\SetterInjection\D',
            array('a' => 'LaminasTest\Di\TestAsset\SetterInjection\A')
        );

        $this->assertSame($d->a, 'LaminasTest\Di\TestAsset\SetterInjection\A');
    }

    /**
     * @group Laminas-308
     */
    public function testWillNotCallStaticInjectionMethods()
    {
        $di = new Di;
        $di->definitions()->addDefinition(new Definition\RuntimeDefinition(), false);
        $di->newInstance('LaminasTest\Di\TestAsset\SetterInjection\StaticSetter', array('name' => 'testName'));

        $this->assertSame(\LaminasTest\Di\TestAsset\SetterInjection\StaticSetter::$name, 'originalName');
    }

    /**
     * @group Laminas-142
     */
    public function testDiWillInjectDefaultParameters()
    {
        $di = new Di;

        $classDef = new Definition\ClassDefinition('LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters');
        $classDef->addMethod('__construct', true);
        $classDef->addMethodParameter(
            '__construct',
            'a',
            array('type' => false, 'required' => false, 'default' => null)
        );
        $classDef->addMethodParameter(
            '__construct',
            'b',
            array('type' => false, 'required' => false, 'default' => 'defaultConstruct')
        );
        $classDef->addMethodParameter(
            '__construct',
            'c',
            array('type' => false, 'required' => false, 'default' => array())
        );

        $di->definitions()->addDefinition($classDef, false);

        $optionalParams = $di->newInstance('LaminasTest\Di\TestAsset\ConstructorInjection\OptionalParameters');

        $this->assertSame(null, $optionalParams->a);
        $this->assertSame('defaultConstruct', $optionalParams->b);
        $this->assertSame(array(), $optionalParams->c);
    }

    /**
     * @group SharedInstance
     */
    public function testGetWithParamsWillUseSharedInstance()
    {
        $di = new Di;

        $sharedInstanceClass = 'LaminasTest\Di\TestAsset\ConstructorInjection\A';
        $retrievedInstanceClass = 'LaminasTest\Di\TestAsset\ConstructorInjection\C';

        // Provide definitions for $retrievedInstanceClass, but not for $sharedInstanceClass.
        $arrayDefinition = array($retrievedInstanceClass => array (
            'supertypes' => array ( ),
            'instantiator' => '__construct',
            'methods' => array ('__construct' => true),
            'parameters' => array ( '__construct' => array (
                "$retrievedInstanceClass::__construct:0" => array ('a', $sharedInstanceClass, true, NULL),
                "$retrievedInstanceClass::__construct:1" => array ('params', NULL, false, array()),
            )),
        ));

        // This also disables scanning of class A.
        $di->setDefinitionList(new DefinitionList(new Definition\ArrayDefinition($arrayDefinition)));

        $di->instanceManager()->addSharedInstance(new $sharedInstanceClass, $sharedInstanceClass);
        $returnedC = $di->get($retrievedInstanceClass, array('params' => array('test')));
        $this->assertInstanceOf($retrievedInstanceClass, $returnedC);
    }
}
