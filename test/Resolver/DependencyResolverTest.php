<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use ArrayIterator;
use ArrayObject;
use IteratorAggregate;
use Laminas\Di\Config;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Definition\ClassDefinitionInterface;
use Laminas\Di\Definition\DefinitionInterface;
use Laminas\Di\Definition\ParameterInterface;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Exception;
use Laminas\Di\Resolver\DependencyResolver;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use stdClass;

use function array_keys;
use function array_merge;
use function array_shift;
use function basename;
use function glob;
use function uniqid;

/**
 * @coversDefaultClass Laminas\Di\Resolver\DependencyResolver
 */
class DependencyResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private function getEmptyContainerMock(): ContainerInterface
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->any())->method('has')->withAnyParameters()->willReturn(false);
        $container->expects($this->never())->method('get')->withAnyParameters();

        return $container;
    }

    private function mockParameter(string $name, int $position, array $options): ParameterInterface
    {
        $definition = array_merge([
            'default'  => null,
            'type'     => null,
            'builtin'  => false,
            'required' => true,
        ], $options);

        $mock = $this->getMockForAbstractClass(ParameterInterface::class);
        $mock->method('getName')->willReturn($name);
        $mock->method('getPosition')->willReturn($position);
        $mock->method('getDefault')->willReturn($definition['default']);
        $mock->method('getType')->willReturn($definition['type']);
        $mock->method('isBuiltin')->willReturn((bool) $definition['builtin']);
        $mock->method('isRequired')->willReturn((bool) $definition['required']);

        return $mock;
    }

    private function mockClassDefinition(
        string $name,
        array $parameters = [],
        array $interfaces = [],
        array $supertypes = []
    ): ClassDefinitionInterface {
        $mock = $this->getMockForAbstractClass(ClassDefinitionInterface::class);

        $mock->method('getInterfaces')->willReturn($interfaces);
        $mock->method('getSupertypes')->willReturn($supertypes);
        $mock->expects($this->never())->method('getReflection');

        $position   = 0;
        $paramMocks = [];

        foreach ($parameters as $name => $options) {
            $paramMocks[] = $this->mockParameter($name, $position++, $options);
        }

        $mock->method('getParameters')->willReturn($paramMocks);

        return $mock;
    }

    /**
     * input:
     *
     * [
     *      'Classname' => [
     *          'interfaces' => [ 'Interface', 'interface2', ... ],
     *          'supertypes' => [ 'Supertype1', 'Supertype2', ... ],
     *          'parameters' => [
     *              'paramName' => [
     *                  'required' => true,
     *                  'builtin'  => true,
     *                  'type'     => 'string',
     *                  'default'  => null,
     *              ],
     *              // ...
     *          ],
     *      ],
     *      // ...
     * ]
     *
     * @return DefinitionInterface
     */
    private function mockDefinition(array $definition)
    {
        $mock = $this->getMockForAbstractClass(DefinitionInterface::class);

        $mock->method('getClasses')->willReturn(array_keys($definition));
        foreach ($definition as $class => $options) {
            $options = array_merge([
                'parameters' => [],
                'interfaces' => [],
                'supertypes' => [],
            ], $options);

            $mock->method('getClassDefinition')
                ->with($class)
                ->willReturn($this->mockClassDefinition(
                    $class,
                    $options['parameters'],
                    $options['interfaces'],
                    $options['supertypes']
                ));
        }

        $mock->method('hasClass')->willReturnCallback(function ($class) use ($definition) {
            return isset($definition[$class]);
        });

        return $mock;
    }

    public function testResolveWithoutConfig()
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());

        $params = $resolver->resolveParameters(TestAsset\B::class);
        $this->assertCount(1, $params);

        $injection = array_shift($params);
        $this->assertInstanceOf(TypeInjection::class, $injection);
        $this->assertEquals(TestAsset\A::class, (string) $injection);

        $params = $resolver->resolveParameters(TestAsset\A::class);
        $this->assertIsArray($params);
        $this->assertCount(0, $params);
    }

    public function testResolveWithContainerFailsWhenMissing()
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());

        $this->expectException(Exception\MissingPropertyException::class);
        $resolver->setContainer($this->getEmptyContainerMock());
        $resolver->resolveParameters(TestAsset\RequiresA::class);
    }

    public function testResolveSucceedsWithoutContainer()
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());
        $result   = $resolver->resolveParameters(TestAsset\RequiresA::class);

        $this->assertCount(1, $result);
        $this->assertIsArray($result);
        $this->assertSame(TestAsset\A::class, (string) $result['p']);
    }

    public function testResolveFailsForDependenciesWithoutType(): void
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());

        $this->expectException(Exception\MissingPropertyException::class);
        $resolver->resolveParameters(TestAsset\Constructor\RequiredArguments::class);
    }

    public function testResolveFailsForInterfaces(): void
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());

        $this->expectException(Exception\ClassNotFoundException::class);
        $resolver->resolveParameters(TestAsset\DummyInterface::class);
    }

    public function provideClassesWithoutConstructionParams(): array
    {
        return [
            'noargs'      => [TestAsset\Constructor\EmptyConstructor::class],
            'noconstruct' => [TestAsset\Constructor\NoConstructor::class],
        ];
    }

    /**
     * @dataProvider provideClassesWithoutConstructionParams
     */
    public function testResolveClassWithoutParameters(string $class): void
    {
        $resolver = new DependencyResolver(new RuntimeDefinition(), new Config());
        $result   = $resolver->resolveParameters($class);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testResolveWithOptionalArgs(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), new Config());
        $result    = $resolver->resolveParameters(TestAsset\Constructor\OptionalArguments::class);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(ValueInjection::class, $result);
        $this->assertSame(null, $result['foo']->toValue($container));
        $this->assertSame('something', $result['bar']->toValue($container));
    }

    public function testResolvePassedDependenciesWithoutType(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $resolver  = new DependencyResolver(new RuntimeDefinition(), new Config());

        $expected = 'Some Value';
        $result   = $resolver->resolveParameters(TestAsset\Constructor\RequiredArguments::class, [
            'anyDep' => $expected,
        ]);

        $this->assertCount(3, $result);
        $this->assertInstanceOf(ValueInjection::class, $result['anyDep']);
        $this->assertSame($expected, $result['anyDep']->toValue($container));
    }

    public function providePreferenceConfigs(): array
    {
        $args = [];

        foreach (glob(__DIR__ . '/../_files/preferences/*.php') as $configFile) {
            $config         = include $configFile;
            $configInstance = new Config($config);
            $name           = basename($configFile, 'php');

            foreach ($config['expect'] as $key => $expectation) {
                [$requested, $expectedResult, $context] = $expectation;
                $args[$name . $key]                     = [
                    $configInstance,
                    $requested,
                    $context,
                    $expectedResult,
                ];
            }
        }

        return $args;
    }

    /**
     * @dataProvider providePreferenceConfigs
     */
    public function testResolveConfiguredPreference(
        ConfigInterface $config,
        string $requestClass,
        ?string $context,
        ?string $expectedType
    ): void {
        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $this->assertSame($expectedType, $resolver->resolvePreference($requestClass, $context));
    }

    public function provideExplicitInjections(): array
    {
        return [
            'type'  => [new TypeInjection(TestAsset\B::class)],
            'value' => [new ValueInjection(new stdClass())],
        ];
    }

    /**
     * @dataProvider provideExplicitInjections
     */
    public function testExplicitInjectionInConfigIsUsedWithoutAdditionalTypeChecks(object $expected): void
    {
        $config = new Config([
            'types' => [
                TestAsset\RequiresA::class => [
                    'parameters' => [
                        'p' => $expected,
                    ],
                ],
            ],
        ]);

        $resolver = new DependencyResolver(new RuntimeDefinition(), $config);
        $result   = $resolver->resolveParameters(TestAsset\RequiresA::class);
        $this->assertArrayHasKey('p', $result);
        $this->assertSame($expected, $result['p']);
    }

    public function provideUnusableParametersData(): array
    {
        return [
            //            [type,               value,                builtIn]
            'string'   => ['string',           123,                  true],
            'int'      => ['int',              'non-numeric value',  true],
            'bool'     => ['bool',             'non boolean string', true],
            'iterable' => ['iterable',         new stdClass(),       true],
            'callable' => ['callable',         new stdClass(),       true],
            'class'    => [TestAsset\A::class, new stdClass(),       false],
        ];
    }

    /**
     * @dataProvider provideUnusableParametersData
     * @param mixed $value
     */
    public function testUnusableConfigParametersThrowsException(string $type, $value, bool $builtin = false): void
    {
        $class      = uniqid('MockedTestClass');
        $paramName  = uniqid('param');
        $config     = $this->getMockBuilder(ConfigInterface::class)->getMockForAbstractClass();
        $definition = $this->mockDefinition([
            $class => [
                'parameters' => [
                    $paramName => [
                        'type'    => $type,
                        'builtin' => $builtin,
                    ],
                ],
            ],
        ]);

        $config->method('isAlias')->willReturn(false);
        $config->expects($this->atLeastOnce())
            ->method('getParameters')
            ->with($class)
            ->willReturn([
                $paramName => $value,
            ]);

        $resolver = new DependencyResolver($definition, $config);

        $this->expectException(Exception\UnexpectedValueException::class);
        $resolver->resolveParameters($class);
    }

    public function provideUsableParametersData()
    {
        // @codingStandardsIgnoreStart
        return [
            //                             [type,               value,                         builtIn]
            'string'                    => ['string',           '123',                         true],
            'int'                       => ['int',              rand(0, 72649), true],
            'floatForInt'               => ['int',              (float) rand(0, 72649) / 10.0, true],
            'intForFloat'               => ['float',            rand(0, 72649), true],
            'float'                     => ['float',            (float) rand(0, 72649) / 10.0, true],

            // Accepted by php as well
            'stringForInt'              => ['int',              '123',                         true],
            'stringForFloat'            => ['float',            '123.78',                      true],

            'boolTrue'                  => ['bool',             false,                         true],
            'boolFalse'                 => ['bool',             true,                          true],
            'iterableArray'             => ['iterable',         [],                            true],
            'iterableIterator'          => ['iterable',         new ArrayIterator([]),         true],
            'iterableIteratorAggregate' => ['iterable',         new class implements IteratorAggregate
            {
                public function getIterator()
                {
                    return new ArrayIterator([]);
                }
            }, true],
            'callableClosure'           => ['callable',         function () {
            }, true],
            'callableString'            => ['callable',         'trim',                        true],
            'callableObject'            => ['callable',         new class
            {
                public function __invoke()
                {
                }
            }, true],
            'derivedInstance'           => [TestAsset\B::class, new TestAsset\ExtendedB(new TestAsset\A()), false],
            'directInstance'            => [TestAsset\A::class, new TestAsset\A(),             false],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider provideUsableParametersData
     * @param mixed $value
     */
    public function testUsableConfigParametersAreAccepted(string $type, $value, bool $builtin = false)
    {
        $class      = uniqid('MockedTestClass');
        $paramName  = uniqid('param');
        $definition = $this->mockDefinition([
            $class => [
                'parameters' => [
                    $paramName => [
                        'type'    => $type,
                        'builtin' => $builtin,
                    ],
                ],
            ],
        ]);

        $config = new Config([
            'types' => [
                $class => [
                    'parameters' => [
                        $paramName => $value,
                    ],
                ],
            ],
        ]);

        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $resolver  = new DependencyResolver($definition, $config);
        $result    = $resolver->resolveParameters($class);

        $this->assertArrayHasKey($paramName, $result);
        $this->assertInstanceOf(ValueInjection::class, $result[$paramName]);
        $this->assertSame($value, $result[$paramName]->toValue($container));
    }

    /**
     * Use Case:
     *
     * - A class requires an interface "A".
     * - The configuration defines this parameter to inject another interface which extends "A"
     *
     * In this case the resolver must accept it.
     */
    public function testConfiguredExtendedInterfaceParameterSatisfiesRequiredInterfaceType()
    {
        $class      = uniqid('MockedTestClass');
        $paramName  = uniqid('param');
        $definition = $this->mockDefinition([
            $class => [
                'parameters' => [
                    $paramName => [
                        'type' => TestAsset\Hierarchy\InterfaceA::class,
                    ],
                ],
            ],
        ]);

        $config = new Config([
            'types' => [
                $class => [
                    'parameters' => [
                        $paramName => TestAsset\Hierarchy\InterfaceC::class,
                    ],
                ],
            ],
        ]);

        $resolver = new DependencyResolver($definition, $config);
        $result   = $resolver->resolveParameters($class);

        $this->assertArrayHasKey($paramName, $result);
        $this->assertInstanceOf(TypeInjection::class, $result[$paramName]);
        $this->assertEquals(TestAsset\Hierarchy\InterfaceC::class, (string) $result[$paramName]);
    }

    public function provideIterableClassNames(): array
    {
        return [
            'iterator'          => [TestAsset\Pseudotypes\IteratorImplementation::class],
            'iteratorAggregate' => [TestAsset\Pseudotypes\IteratorAggregateImplementation::class],
            'arrayObject'       => [ArrayObject::class],
            'arrayIterator'     => [ArrayIterator::class],
        ];
    }

    /**
     * Scenario:
     *
     * - A class requires an iterable
     * - The configuration defines this parameter to inject a type that implement Traversable
     *
     * In this case the resolver must accept it.
     *
     * @dataProvider provideIterableClassNames
     */
    public function testConfiguredTraversableTypeParameterSatisfiesIterable(string $iterableClassName)
    {
        $class      = TestAsset\IterableDependency::class;
        $paramName  = 'iterator';
        $definition = new RuntimeDefinition();
        $config     = new Config([
            'types' => [
                $class => [
                    'parameters' => [
                        $paramName => $iterableClassName,
                    ],
                ],
            ],
        ]);

        $resolver = new DependencyResolver($definition, $config);
        $result   = $resolver->resolveParameters($class);

        $this->assertArrayHasKey($paramName, $result);
        $this->assertInstanceOf(TypeInjection::class, $result[$paramName]);
        $this->assertEquals($iterableClassName, (string) $result[$paramName]);
    }

    /**
     * Scenario:
     *
     * - A class requires a callable
     * - The configuration defines this parameter to inject a class that implements __invoke()
     *
     * In this case the resolver must accept it.
     */
    public function testConfiguredInvokableTypeParameterSatisfiesCallable()
    {
        $class      = uniqid('MockedTestClass');
        $paramName  = uniqid('param');
        $definition = $this->mockDefinition([
            $class => [
                'parameters' => [
                    $paramName => [
                        'type' => 'callable',
                    ],
                ],
            ],
        ]);

        $config = new Config([
            'types' => [
                $class           => [
                    'parameters' => [
                        $paramName => TestAsset\Pseudotypes\CallableImplementation::class,
                    ],
                ],
                'Callable.Alias' => [
                    'typeOf' => TestAsset\Pseudotypes\CallableImplementation::class,
                ],
            ],
        ]);

        $resolver = new DependencyResolver($definition, $config);
        $result   = $resolver->resolveParameters($class);

        $this->assertArrayHasKey($paramName, $result);
        $this->assertInstanceOf(TypeInjection::class, $result[$paramName]);
        $this->assertEquals(TestAsset\Pseudotypes\CallableImplementation::class, (string) $result[$paramName]);
    }

    /**
     * Scenario:
     *
     * - A class requires a callable
     * - The configuration defines this parameter to inject an alias that
     *   points to a class which implements __invoke()
     *
     * In this case the resolver must accept it.
     */
    public function testConfiguredInvokableAliasParameterSatisfiesCallable()
    {
        $class      = uniqid('MockedTestClass');
        $paramName  = uniqid('param');
        $definition = $this->mockDefinition([
            $class => [
                'parameters' => [
                    $paramName => [
                        'type' => 'callable',
                    ],
                ],
            ],
        ]);

        $config = new Config([
            'types' => [
                $class           => [
                    'parameters' => [
                        $paramName => 'Callable.Alias',
                    ],
                ],
                'Callable.Alias' => [
                    'typeOf' => TestAsset\Pseudotypes\CallableImplementation::class,
                ],
            ],
        ]);

        $resolver = new DependencyResolver($definition, $config);
        $result   = $resolver->resolveParameters($class);

        $this->assertArrayHasKey($paramName, $result);
        $this->assertInstanceOf(TypeInjection::class, $result[$paramName]);
        $this->assertEquals('Callable.Alias', (string) $result[$paramName]);
    }

    public function testResolvePreferenceUsesSupertypes()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(TestAsset\B::class, TestAsset\ExtendedB::class, TestAsset\Hierarchy\A::class);
        $resolver = new DependencyResolver($definition, $config);

        $this->assertEquals(
            TestAsset\ExtendedB::class,
            $resolver->resolvePreference(TestAsset\B::class, TestAsset\Hierarchy\C::class)
        );
    }

    public function testResolvePreferenceUsesInterfaces()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(
            TestAsset\B::class,
            TestAsset\ExtendedB::class,
            TestAsset\Hierarchy\InterfaceA::class
        );

        $resolver = new DependencyResolver($definition, $config);

        $this->assertEquals(
            TestAsset\ExtendedB::class,
            $resolver->resolvePreference(TestAsset\B::class, TestAsset\Hierarchy\C::class)
        );
    }

    public function testParametresResolverShouldNotCheckTheTypeForString()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setParameters(
            TestAsset\B::class,
            ['a' => 'my-service']
        );

        $resolver = new DependencyResolver($definition, $config);

        $parameters = $resolver->resolveParameters(TestAsset\B::class);

        $this->assertCount(1, $parameters);
        $this->assertInstanceOf(TypeInjection::class, $parameters['a']);
        $this->assertSame('my-service', $parameters['a']->__toString());
    }

    /**
     * Ensures the documented preference resolver behavior as documented
     *
     * @see https://docs.laminas.dev/laminas-di/config/#type-preferences
     */
    public function testResolvePreferenceFallsBackToGlobalPreferenceWhenNotSuitableForClassRequirement()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(TestAsset\A::class, TestAsset\B::class, TestAsset\RequiresA::class);
        $config->setTypePreference(TestAsset\A::class, TestAsset\ExtendedA::class);
        $resolver = new DependencyResolver($definition, $config);

        $this->assertSame(
            TestAsset\ExtendedA::class,
            $resolver->resolvePreference(TestAsset\A::class, TestAsset\RequiresA::class)
        );
    }

    /**
     * Ensures the documented preference resolver behavior as documented
     *
     * @see https://docs.laminas.dev/laminas-di/config/#type-preferences
     */
    public function testResolvePreferenceReturnsNullWhenNothingIsSuitableForClassRequirement()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(TestAsset\A::class, TestAsset\ExtendedB::class, TestAsset\RequiresA::class);
        $config->setTypePreference(TestAsset\A::class, TestAsset\B::class);
        $resolver = new DependencyResolver($definition, $config);

        $this->assertNull($resolver->resolvePreference(TestAsset\A::class, TestAsset\RequiresA::class));
    }

    /**
     * Ensures the documented preference resolver behavior as documented
     *
     * @see https://docs.laminas.dev/laminas-di/config/#type-preferences
     */
    public function testResolvePreferenceFallsBackToGlobalPreferenceWhenNotSuitableForInterfaceRequirement()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(
            TestAsset\Hierarchy\InterfaceB::class,
            TestAsset\Hierarchy\InterfaceA::class,
            TestAsset\A::class
        );
        $config->setTypePreference(TestAsset\Hierarchy\InterfaceB::class, TestAsset\Hierarchy\InterfaceC::class);
        $resolver = new DependencyResolver($definition, $config);

        $this->assertSame(
            TestAsset\Hierarchy\InterfaceC::class,
            $resolver->resolvePreference(TestAsset\Hierarchy\InterfaceB::class, TestAsset\A::class)
        );
    }

    /**
     * Ensures the documented preference resolver behavior as documented
     *
     * @see https://docs.laminas.dev/laminas-di/config/#type-preferences
     */
    public function testResolvePreferenceReturnsNullWhenNothingIsSuitableForInterfaceRequirement()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(
            TestAsset\Hierarchy\InterfaceB::class,
            TestAsset\B::class,
            TestAsset\A::class
        );
        $config->setTypePreference(
            TestAsset\Hierarchy\InterfaceB::class,
            TestAsset\Hierarchy\InterfaceA::class
        );
        $resolver = new DependencyResolver($definition, $config);

        $this->assertNull(
            $resolver->resolvePreference(TestAsset\Hierarchy\InterfaceB::class, TestAsset\A::class)
        );
    }

    public function testResolvePreferenceUsesDefinedClassForInterfaceRequirements()
    {
        $definition = new RuntimeDefinition();
        $config     = new Config();
        $config->setTypePreference(
            TestAsset\Hierarchy\InterfaceB::class,
            TestAsset\Hierarchy\B::class
        );

        $resolver = new DependencyResolver($definition, $config);

        $this->assertSame(
            TestAsset\Hierarchy\B::class,
            $resolver->resolvePreference(TestAsset\Hierarchy\InterfaceB::class, TestAsset\A::class)
        );
    }
}
