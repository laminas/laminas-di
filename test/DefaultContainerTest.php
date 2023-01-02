<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use Laminas\Di\DefaultContainer;
use Laminas\Di\InjectorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

use function uniqid;

/**
 * @coversDefaultClass Laminas\Di\DefaultContainer
 */
class DefaultContainerTest extends TestCase
{
    /**
     * @return MockObject&InjectorInterface
     */
    private function mockInjector()
    {
        return $this->getMockForAbstractClass(InjectorInterface::class);
    }

    /**
     * Tests DefaultContainer->setInstance()
     */
    public function testSetInstance(): void
    {
        $injector = $this->mockInjector();
        $injector->expects($this->never())->method($this->logicalNot($this->equalTo('')));
        $container = new DefaultContainer($injector);
        $expected  = new stdClass();
        $key       = uniqid('Test');

        $container->setInstance($key, $expected);
        $this->assertTrue($container->has($key));
        $this->assertSame($expected, $container->get($key));
    }

    /**
     * Tests DefaultContainer->has()
     */
    public function testHasConsultatesInjector(): void
    {
        $injector = $this->mockInjector();
        $key      = uniqid('TestClass');

        $injector->expects($this->atLeastOnce())
            ->method('canCreate')
            ->with($key)
            ->willReturn(true);

        $injector2 = $this->mockInjector();
        $injector2->expects($this->atLeastOnce())
            ->method('canCreate')
            ->with($key)
            ->willReturn(false);

        $container  = new DefaultContainer($injector);
        $container2 = new DefaultContainer($injector2);
        $this->assertTrue($container->has($key));
        $this->assertFalse($container2->has($key));
    }

    /**
     * Tests DefaultContainer->get()
     */
    public function testGetUsesInjector(): void
    {
        $injector = $this->mockInjector();
        $key      = uniqid('TestClass');
        $expected = new stdClass();

        $injector->expects($this->atLeastOnce())
            ->method('create')
            ->with($key)
            ->willReturn($expected);

        $this->assertSame($expected, (new DefaultContainer($injector))->get($key));
    }

    /**
     * Tests DefaultContainer->get()
     */
    public function testGetInstanciatesOnlyOnce(): void
    {
        $injector = $this->mockInjector();
        $key      = uniqid('TestClass');

        $injector->expects($this->once())
            ->method('create')
            ->with($key)
            ->willReturnCallback(static fn(): stdClass => new stdClass());

        $container = new DefaultContainer($injector);

        /** @psalm-var mixed */
        $expected = $container->get($key);

        $this->assertSame($expected, $container->get($key));
    }
}
