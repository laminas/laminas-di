<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\AbstractInjector;
use Laminas\Di\CodeGenerator\FactoryInterface;
use Laminas\Di\DefaultContainer;
use Laminas\Di\InjectorInterface;
use LaminasTest\Di\TestAsset\CodeGenerator\StdClassFactory;
use LaminasTest\Di\TestAsset\InvokableInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use stdClass;

use function uniqid;

/**
 * @covers \Laminas\Di\CodeGenerator\AbstractInjector
 */
class AbstractInjectorTest extends TestCase
{
    /**
     * @var InjectorInterface|ObjectProphecy
     */
    private $decoratedInjectorProphecy;

    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $containerProphecy;

    protected function setUp(): void
    {
        $this->decoratedInjectorProphecy = $this->prophesize(InjectorInterface::class);
        $this->containerProphecy = $this->prophesize(ContainerInterface::class);

        parent::setUp();
    }

    public function createTestSubject(callable $factoriesProvider, bool $withContainer = true): AbstractInjector
    {
        $injector = $this->decoratedInjectorProphecy->reveal();
        $container = $withContainer ? $this->containerProphecy->reveal() : null;

        return new class($factoriesProvider, $injector, $container) extends AbstractInjector
        {
            private $provider;

            public function __construct(
                callable $provider,
                InjectorInterface $injector,
                ContainerInterface $container = null
            ) {
                $this->provider = $provider;
                parent::__construct($injector, $container);
            }

            public function exposeFactoryValue(string $key)
            {
                return $this->factories[$key] ?? null;
            }

            protected function loadFactoryList() : void
            {
                $this->factories = ($this->provider)();
            }
        };
    }

    public function testImplementsContract()
    {
        $prophecy = $this->prophesize(InvokableInterface::class);
        $prophecy->__invoke()
            ->shouldBeCalled()
            ->willReturn([
                'SomeService' => 'SomeFactory'
            ]);

        $subject = $this->createTestSubject($prophecy->reveal());
        $this->assertInstanceOf(InjectorInterface::class, $subject);
    }

    public function testCanCreateReturnsTrueWhenAFactoryIsAvailable()
    {
        $className = uniqid('SomeClass');
        $provider = function () use ($className) {
            return [$className => 'SomeClassFactory'];
        };

        $this->decoratedInjectorProphecy
            ->canCreate($className)
            ->shouldNotBeCalled();

        $subject = $this->createTestSubject($provider);
        $this->assertTrue($subject->canCreate($className));
    }

    public function testCanCreateUsesDecoratedInjectorWithoutFactory()
    {
        $missingClass = uniqid('SomeClass');
        $existingClass = uniqid('SomeOtherClass');
        $provider = function () {
            return [];
        };

        $this->decoratedInjectorProphecy
            ->canCreate($missingClass)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->decoratedInjectorProphecy
            ->canCreate($existingClass)
            ->shouldBeCalled()
            ->willReturn(true);

        $subject = $this->createTestSubject($provider);

        $this->assertTrue($subject->canCreate($existingClass));
        $this->assertFalse($subject->canCreate($missingClass));
    }

    public function testCreateUsesFactory()
    {
        $factory = $this->prophesize(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $params = ['someArg' => uniqid()];
        $expected = new stdClass();
        $provider = function () use ($className, $factory) {
            return [$className => $factory->reveal()];
        };

        $factory
            ->create(
                $this->containerProphecy->reveal(),
                $params
            )
            ->shouldBeCalled()
            ->willReturn($expected);

        $this->decoratedInjectorProphecy
            ->create($className, Argument::cetera())
            ->shouldNotBeCalled();

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testCreateUsesDecoratedInjectorIfNoFactoryIsAvailable()
    {
        $className = uniqid('SomeClass');
        $expected = new stdClass();
        $params = [ 'someArg' => uniqid() ];
        $provider = function () {
            return [];
        };

        $this->decoratedInjectorProphecy
            ->create($className, $params)
            ->shouldBeCalled()
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testConstructionWithoutContainerUsesDefaultContainer()
    {
        $factory = $this->prophesize(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $expected = new stdClass();
        $provider = function () use ($className, $factory) {
            return [$className => $factory->reveal()];
        };

        $factory->create(Argument::type(DefaultContainer::class), Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider, false);
        $this->assertSame($expected, $subject->create($className));
    }

    public function testFactoryIsCreatedFromClassNameString()
    {
        $subject = $this->createTestSubject(function () {
            return ['SomeClass' => StdClassFactory::class ];
        });

        $this->assertSame(StdClassFactory::class, $subject->exposeFactoryValue('SomeClass'));
        $this->assertInstanceOf(stdClass::class, $subject->create('SomeClass'));
        $this->assertInstanceOf(StdClassFactory::class, $subject->exposeFactoryValue('SomeClass'));
    }
}
