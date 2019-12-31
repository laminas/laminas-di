<?php
namespace LaminasTest\Di\ServiceLocator;

use Laminas\Di\Di;
use Laminas\Di\ServiceLocator\DependencyInjectorProxy;
use LaminasTest\Di\TestAsset\SetterInjection\A;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests used to verify DependencyInjectorProxy functionality
 */
class DependencyInjectorProxyTest extends TestCase
{
    public function testWillDiscoverInjectedMethodParameters()
    {
        $di = new Di();
        $a = new A();
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\B',
            array('a' => $a)
        );
        $proxy = new DependencyInjectorProxy($di);
        $b = $proxy->get('LaminasTest\Di\TestAsset\SetterInjection\B');
        $methods = $b->getMethods();
        $this->assertSame('setA', $methods[0]['method']);
        $this->assertSame($a, $methods[0]['params'][0]);
    }
}
