<?php

declare(strict_types=1);

namespace LaminasTest\Di\CodeGenerator;

use Laminas\Di\CodeGenerator\AbstractInjector;
use Laminas\Di\CodeGenerator\FactoryInterface;
use Laminas\Di\DefaultContainer;
use Laminas\Di\InjectorInterface;
use LaminasTest\Di\TestAsset\CodeGenerator\StdClassFactory;
use LaminasTest\Di\TestAsset\InvokableInterface;
use PHPUnit\Framework\MockObject\MockObject;
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
    /** @var InjectorInterface&MockObject */
    private InjectorInterface $decoratedInjector;
    /** @var ContainerInterface&MockObject */
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->decoratedInjector = $this->createMock(InjectorInterface::class);
        $this->container         = $this->createMock(ContainerInterface::class);

        parent::setUp();
    }

    public function createTestSubject(callable $factoriesProvider, bool $withContainer = true): AbstractInjector
    {
        $injector  = $this->decoratedInjector;
        $container = $withContainer ? $this->container : null;

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
        $invokable = $this->createMock(InvokableInterface::class);
        $invokable
            ->expects(self::atLeastOnce())
            ->method('__invoke')
            ->willReturn([
                'SomeService' => 'SomeFactory',
            ]);

        $subject = $this->createTestSubject($invokable);
        $this->assertInstanceOf(InjectorInterface::class, $subject);
    }

    public function testCanCreateReturnsTrueWhenAFactoryIsAvailable()
    {
        $className = uniqid('SomeClass');
        $provider  = fn() => [$className => 'SomeClassFactory'];

        $this->decoratedInjector
            ->expects(self::never())
            ->method('canCreate')
            ->with($className);

        $subject = $this->createTestSubject($provider);
        $this->assertTrue($subject->canCreate($className));
    }

    public function testCanCreateUsesDecoratedInjectorWithoutFactory()
    {
        $missingClass  = uniqid('SomeClass');
        $existingClass = 'stdClass';
        $provider      = fn() => [];

        $this->decoratedInjector
            ->expects(self::exactly(2))
            ->method('canCreate')
            ->with(self::logicalOr($existingClass, $missingClass))
            ->willReturnMap([
                [$existingClass, true],
                [$missingClass, false],
            ]);

        $subject = $this->createTestSubject($provider);

        $this->assertTrue($subject->canCreate($existingClass));
        $this->assertFalse($subject->canCreate($missingClass));
    }

    public function testCreateUsesFactory()
    {
        $factory   = $this->createMock(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $params    = ['someArg' => uniqid()];
        $expected  = new stdClass();
        $provider  = fn() => [$className => $factory];

        $factory
            ->expects(self::once())
            ->method('create')
            ->with($this->container, $params)
            ->willReturn($expected);

        $this->decoratedInjector
            ->expects(self::never())
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

        $this->decoratedInjector
            ->expects(self::once())
            ->method('create')
            ->with($className, $params)
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testConstructionWithoutContainerUsesDefaultContainer()
    {
        $factory   = $this->createMock(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $expected  = new stdClass();
        $provider  = fn() => [$className => $factory];

        $factory
            ->expects(self::once())
            ->method('create')
            ->with(self::isInstanceOf(DefaultContainer::class))
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider, false);
        $this->assertSame($expected, $subject->create($className));
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
