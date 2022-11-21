<?php

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\AbstractInjector;
use Laminas\Di\CodeGenerator\FactoryInterface;
use Laminas\Di\DefaultContainer;
use Laminas\Di\InjectorInterface;
use LaminasTest\Di\TestAsset\CodeGenerator\StdClassFactory;
use LaminasTest\Di\TestAsset\InvokableInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionProperty;
use stdClass;

use function uniqid;

/**
 * @covers \Laminas\Di\CodeGenerator\AbstractInjector
 */
class AbstractInjectorTest extends TestCase
{
    /** @var InjectorInterface */
    private $decoratedInjectorProphecy;

    /** @var ContainerInterface */
    private $containerProphecy;

    protected function setUp(): void
    {
        $this->decoratedInjectorProphecy = $this->createMock(InjectorInterface::class);
        $this->containerProphecy         = $this->createMock(ContainerInterface::class);
        parent::setUp();
    }

    public function createTestSubject(callable $factoriesProvider, bool $withContainer = true): AbstractInjector
    {
        $injector  = $this->decoratedInjectorProphecy;
        $container = $withContainer ? $this->containerProphecy : null;

        return new class ($factoriesProvider, $injector, $container) extends AbstractInjector
        {
            /** @var callable */
            private $provider;

            public function __construct(
                callable $provider,
                InjectorInterface $injector,
                ?ContainerInterface $container = null
            ) {
                $this->provider = $provider;
                parent::__construct($injector, $container);
            }

            protected function loadFactoryList(): void
            {
                $this->factories = ($this->provider)();
            }
        };
    }

    public function testImplementsContract()
    {
        $prophecy = $this->createMock(InvokableInterface::class);
        $prophecy
            ->method('__invoke')
            ->willReturn([
                'SomeService' => 'SomeFactory',
            ]);

        $subject = $this->createTestSubject($prophecy);
        $this->assertInstanceOf(InjectorInterface::class, $subject);
    }

    public function testCanCreateReturnsTrueWhenAFactoryIsAvailable()
    {
        $className = uniqid('SomeClass');
        $provider  = fn() => [$className => 'SomeClassFactory'];

        $this->decoratedInjectorProphecy
            ->expects($this->never())
            ->method('canCreate')
            ->with($className);

        $subject = $this->createTestSubject($provider);
        $this->assertTrue($subject->canCreate($className));
    }

    public function testCanCreateUsesDecoratedInjectorWithoutFactory()
    {
        $existingClass = 'stdClass';
        $provider      = fn() => [];
        $this->decoratedInjectorProphecy
            ->method('canCreate')
            ->with($existingClass)
            ->willReturn(true);
        $subject       = $this->createTestSubject($provider);

        $this->assertTrue($subject->canCreate($existingClass));
    }

    public function testCreateUsesFactory()
    {
        $factory   = $this->createMock(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $params    = ['someArg' => uniqid()];
        $expected  = new stdClass();
        $provider  = fn() => [$className => $factory];

        $factory
            ->method('create')
            ->with($this->containerProphecy, $params)
            ->willReturn($expected);

        $this->decoratedInjectorProphecy
            ->expects($this->never())
            ->method('create')
            ->with($className, []);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testCreateUsesDecoratedInjectorIfNoFactoryIsAvailable()
    {
        $className = uniqid('SomeClass');
        $expected  = new stdClass();
        $params    = ['someArg' => uniqid()];
        $provider  = fn() => [];

        $this->decoratedInjectorProphecy
            ->method('create')
            ->with($className, $params)
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testConstructionWithoutContainerUsesDefaultContainer()
    {
        $factory                 = $this->createMock(FactoryInterface::class);
        $className               = uniqid('SomeClass');
        $params                  = ['someArg' => uniqid()];
        $expected                = new stdClass();
        $provider                = fn() => [$className => $factory];
        $this->containerProphecy = new DefaultContainer($this->decoratedInjectorProphecy);
        $factory
            ->method('create')
            ->with($this->containerProphecy, $params)
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testFactoryIsCreatedFromClassNameString()
    {
        $subject = $this->createTestSubject(fn() => ['SomeClass' => StdClassFactory::class]);

        $factoryInstancesProperty = new ReflectionProperty(AbstractInjector::class, 'factoryInstances');
        $factoriesProperty        = new ReflectionProperty(AbstractInjector::class, 'factories');
        $factoryInstancesProperty->setAccessible(true);
        $factoriesProperty->setAccessible(true);

        $this->assertSame(
            StdClassFactory::class,
            $factoriesProperty->getValue($subject)['SomeClass'] ?? null
        );
        $this->assertInstanceOf(stdClass::class, $subject->create('SomeClass'));
        $this->assertInstanceOf(
            StdClassFactory::class,
            $factoryInstancesProperty->getValue($subject)['SomeClass'] ?? null
        );
    }
}
