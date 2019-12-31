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

class DiCompatibilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider providesSimpleClasses
     * @param string $class
     */
    public function testDiSimple($class)
    {
        $di = new Di();

        $bareObject = new $class;

        $diObject = $di->get($class);

        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     * provides known classes invokable without parameters
     *
     * @return array
     */
    public function providesSimpleClasses()
    {
        return array(
            array('Laminas\Di\Di'),
            array('Laminas\EventManager\EventManager'),
            array('Laminas\Filter\Null'),
            array('Laminas\Form\Form'),
            array('Laminas\Log\Logger'),
            array('Laminas\Stdlib\SplStack'),
            array('Laminas\View\Model\ViewModel'),
        );
    }

    /**
     *
     * error: Missing argument 1 for $class::__construct()
     * @dataProvider providesClassWithConstructionParameters
     * @expectedException PHPUnit_Framework_Error
     * @param string $class
     */
    public function testRaiseErrorMissingConstructorRequiredParameter($class)
    {
        $bareObject = new $class;
        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @expectedException \Laminas\Di\Exception\MissingPropertyException
     * @param string $class
     */
    public function testWillThrowExceptionMissingConstructorRequiredParameterWithDi($class)
    {
        $di = new Di();
        $diObject = $di->get($class);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameter($class, $args)
    {
        $reflection = new \ReflectionClass($class);
        $bareObject = $reflection->newInstanceArgs($args);
        $this->assertInstanceOf($class, $bareObject, 'Test instantiate with constructor required parameters');
    }

    /**
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameterWithDi($class, $args)
    {
        $di = new Di();
        $diObject = $di->get($class, $args);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get with constructor required paramters');
    }

    public function providesClassWithConstructionParameters()
    {
        $serviceManager = new \Laminas\ServiceManager\ServiceManager;
        $serviceManager->setService('EventManager', new \Laminas\EventManager\EventManager);
        $serviceManager->setService('Request', new \stdClass);
        $serviceManager->setService('Response', new \stdClass);

        return array(
            array('Laminas\Config\Config', array('array' => array())),
            array('Laminas\Db\Adapter\Adapter', array('driver' => array('driver' => 'Pdo_Sqlite'))),
            array('Laminas\Mvc\Application', array('configuration' => array(), 'serviceManager' => $serviceManager)),
        );
    }
}
