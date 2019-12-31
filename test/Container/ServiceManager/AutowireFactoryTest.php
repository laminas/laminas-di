<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\Container\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\Di\Container\AutowireFactory as GenericAutowireFactory;
use Laminas\Di\Container\ServiceManager\AutowireFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * AutowireFactory test case.
 *
 * @coversDefaultClass Laminas\Di\Container\ServiceManager\AutowireFactory
 */
class AutowireFactoryTest extends TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createGenericFactoryMock()
    {
        return $this->getMockBuilder(GenericAutowireFactory::class)
            ->setMethodsExcept()
            ->getMock();
    }

    public function testInvokeIsPassedToGenericFactory()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $mock = $this->createGenericFactoryMock();
        $expected = new stdClass();
        $className = 'AnyClassName';

        // Container must not be called directly
        $container->expects($this->never())->method('has');
        $container->expects($this->never())->method('get');

        $mock->expects($this->once())
            ->method('create')
            ->with($container, $className)
            ->willReturn($expected);

        $factory = new AutowireFactory($mock);

        $this->assertSame($expected, $factory($container, $className));
    }

    public function testCanCreateIsPassedToGenericFactory()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $mock = $this->createGenericFactoryMock();
        $className = 'AnyClassName';

        // Container must not be called directly
        $container->expects($this->never())->method('has');
        $container->expects($this->never())->method('get');

        $mock->expects($this->once())
            ->method('canCreate')
            ->with($container, $className)
            ->willReturn(true);

        $factory = new AutowireFactory($mock);

        $this->assertTrue($factory->canCreate($container, $className));
    }
}
