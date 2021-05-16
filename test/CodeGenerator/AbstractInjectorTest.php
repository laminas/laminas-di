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
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use ReflectionProperty;
use stdClass;

use function uniqid;

/**
 * @covers \Laminas\Di\CodeGenerator\AbstractInjector
 */
class AbstractInjectorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<InjectorInterface> */
    private $decoratedInjectorProphecy;

    /** @var ObjectProphecy<ContainerInterface> */
    private $containerProphecy;

    protected function setUp(): void
    {
        $this->decoratedInjectorProphecy = $this->prophesize(InjectorInterface::class);
        $this->containerProphecy         = $this->prophesize(ContainerInterface::class);

        parent::setUp();
    }

    /**
     * @param callable():array<string, class-string<FactoryInterface>|FactoryInterface> $factoriesProvider
     */
    public function createTestSubject(callable $factoriesProvider, bool $withContainer = true): AbstractInjector
    {
        $injector  = $this->decoratedInjectorProphecy->reveal();
        $container = $withContainer ? $this->containerProphecy->reveal() : null;

        return new class ($factoriesProvider, $injector, $container) extends AbstractInjector
        {
            /** @var callable():array<string, class-string<FactoryInterface>|FactoryInterface> */
            private $provider;

            /**
             * @param callable():array<string, class-string<FactoryInterface>|FactoryInterface> $provider
             */
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

    public function testImplementsContract(): void
    {
        $prophecy = $this->prophesize(InvokableInterface::class);
        $prophecy->__invoke()
            ->shouldBeCalled()
            ->willReturn([
                'SomeService' => 'SomeFactory',
            ]);

        $subject = $this->createTestSubject($prophecy->reveal());
        $this->assertInstanceOf(InjectorInterface::class, $subject);
    }

    public function testCanCreateReturnsTrueWhenAFactoryIsAvailable(): void
    {
        $className = uniqid('SomeClass');
        $provider  = function () use ($className): array {
            /** @psalm-var array<string, class-string<FactoryInterface>|FactoryInterface> */
            return [$className => 'SomeClassFactory'];
        };

        $this->decoratedInjectorProphecy
            ->canCreate($className)
            ->shouldNotBeCalled();

        $subject = $this->createTestSubject($provider);
        $this->assertTrue($subject->canCreate($className));
    }

    public function testCanCreateUsesDecoratedInjectorWithoutFactory(): void
    {
        $missingClass  = uniqid('SomeClass');
        $existingClass = uniqid('SomeOtherClass');
        $provider      = function (): array {
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

    public function testCreateUsesFactory(): void
    {
        $factory   = $this->prophesize(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $params    = ['someArg' => uniqid()];
        $expected  = new stdClass();
        $provider  = function () use ($className, $factory): array {
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

    public function testCreateUsesDecoratedInjectorIfNoFactoryIsAvailable(): void
    {
        $className = uniqid('SomeClass');
        $expected  = new stdClass();
        $params    = ['someArg' => uniqid()];
        $provider  = function (): array {
            return [];
        };

        $this->decoratedInjectorProphecy
            ->create($className, $params)
            ->shouldBeCalled()
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider);
        $this->assertSame($expected, $subject->create($className, $params));
    }

    public function testConstructionWithoutContainerUsesDefaultContainer(): void
    {
        $factory   = $this->prophesize(FactoryInterface::class);
        $className = uniqid('SomeClass');
        $expected  = new stdClass();
        $provider  = function () use ($className, $factory): array {
            return [$className => $factory->reveal()];
        };

        $factory->create(Argument::type(DefaultContainer::class), Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($expected);

        $subject = $this->createTestSubject($provider, false);
        $this->assertSame($expected, $subject->create($className));
    }

    public function testFactoryIsCreatedFromClassNameString(): void
    {
        $subject = $this->createTestSubject(function () {
            return ['SomeClass' => StdClassFactory::class];
        });

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
