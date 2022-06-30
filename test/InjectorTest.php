<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use Laminas\Di\Config;
use Laminas\Di\DefaultContainer;
use Laminas\Di\Definition\DefinitionInterface;
use Laminas\Di\Exception;
use Laminas\Di\Exception\ClassNotFoundException;
use Laminas\Di\Injector;
use Laminas\Di\Resolver\DependencyResolverInterface;
use Laminas\Di\Resolver\TypeInjection;
use LaminasTest\Di\TestAsset\DependencyTree as TreeTestAsset;
use PHPUnit\Framework\Constraint;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use stdClass;
use TypeError;

use function array_map;
use function uniqid;

/**
 * @coversDefaultClass \Laminas\Di\Injector
 */
final class InjectorTest extends TestCase
{
    /**
     * @param mixed $value
     * @return Constraint\IsIdentical
     */
    private function isIdentical($value)
    {
        return new Constraint\IsIdentical($value);
    }

    public function testSetContainerReplacesConstructed()
    {
        $mock1 = $this->getMockForAbstractClass(ContainerInterface::class);
        $mock2 = $this->getMockForAbstractClass(ContainerInterface::class);

        $injector = new Injector(null, $mock1);
        $injector->setContainer($mock2);

        $this->assertSame($mock2, $injector->getContainer());
        $this->assertNotSame($mock1, $injector->getContainer());
    }

    public function testConstructWithContainerPassesItToResolver()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $resolver  = $this->getMockForAbstractClass(DependencyResolverInterface::class);
        $resolver->expects($this->once())
            ->method('setContainer')
            ->with($this->isIdentical($container))
            ->willReturnSelf();

        $injector = new Injector(null, $container, null, $resolver);
        $this->assertSame($container, $injector->getContainer());
    }

    public function testSetContainerPassesItToResolver()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $resolver  = $this->getMockForAbstractClass(DependencyResolverInterface::class);
        $injector  = new Injector(null, null, null, $resolver);

        $resolver->expects($this->once())
            ->method('setContainer')
            ->with($this->isIdentical($container))
            ->willReturnSelf();

        $injector->setContainer($container);
        $this->assertSame($container, $injector->getContainer());
    }

    /**
     * @return string[][]
     */
    public function provideClassNames()
    {
        return [
            'simple'   => [TestAsset\A::class],
            'withDeps' => [TestAsset\B::class],
            'derived'  => [TestAsset\Option1ForA::class],
        ];
    }

    /**
     * @dataProvider provideClassNames
     */
    public function testCanCreateReturnsTrueForClasses(string $className)
    {
        $this->assertTrue((new Injector())->canCreate($className));
    }

    public function testCanCreateReturnsFalseForInterfaces()
    {
        $this->assertFalse((new Injector())->canCreate(TestAsset\DummyInterface::class));
    }

    public function testCanCreateReturnsFalseForNonExistingClassOrAlias()
    {
        $injector = new Injector();
        $this->assertFalse($injector->canCreate('Laminas\Di\TestAsset\NoSuchClass'));
        $this->assertFalse($injector->canCreate('Some.Alias.Name'));
    }

    public function provideValidAliases(): array
    {
        return [
            //               [ alias,               target]
            'dotted'      => ['Foo.Alias',          TestAsset\A::class],
            'underscored' => ['Bar_Alias',          TestAsset\B::class],
            'backspaced'  => ['Some\\Custom\\Name', TestAsset\Constructor\EmptyConstructor::class],
            'plain'       => ['BazAlias',           TestAsset\B::class],
        ];
    }

    /**
     * @dataProvider provideValidAliases
     */
    public function testCanCreateReturnsTrueWithDefinedAndValidAliases(string $aliasName, string $className)
    {
        $config = new Config([
            'types' => [
                $aliasName => [
                    'typeOf' => $className,
                ],
            ],
        ]);

        $this->assertTrue((new Injector($config))->canCreate($aliasName));
    }

    public function testCanCreateReturnsFalseWithDefinedInvalidAliases()
    {
        $config = new Config([
            'types' => [
                'Some.Custom.Name' => [
                    'typeOf' => 'LaminasTest\Di\TestAsset\NoSuchClassName',
                ],
            ],
        ]);

        $this->assertFalse((new Injector($config))->canCreate('Some.Custom.Name'));
    }

    public function testCreateWithoutDependencies()
    {
        $result = (new Injector())->create(TestAsset\Constructor\EmptyConstructor::class);
        $this->assertInstanceOf(TestAsset\Constructor\EmptyConstructor::class, $result);
    }

    public function testCreateUsesContainerDependency()
    {
        $injector  = new Injector();
        $expectedA = new TestAsset\A();
        $container = new DefaultContainer($injector);

        $container->setInstance(TestAsset\A::class, $expectedA);
        $injector->setContainer($container);

        /** @var TestAsset\B $result */
        $result = $injector->create(TestAsset\B::class);

        $this->assertInstanceOf(TestAsset\B::class, $result);
        $this->assertSame($expectedA, $result->injectedA);
    }

    public function testCreateSimpleDependency()
    {
        /** @var TestAsset\B $result */
        $result = (new Injector())->create(TestAsset\B::class);

        $this->assertInstanceOf(TestAsset\B::class, $result);
        $this->assertInstanceOf(TestAsset\A::class, $result->injectedA);
    }

    public function provideCircularClasses(): array
    {
        $classes = [
            'flat'         => TestAsset\CircularClasses\A::class,
            'deep'         => TestAsset\CircularClasses\C::class,
            'self'         => TestAsset\CircularClasses\X::class,
            'selfOptional' => TestAsset\CircularClasses\Y::class,
        ];

        return array_map(fn($class) => [$class], $classes);
    }

    /**
     * @dataProvider provideCircularClasses
     */
    public function testCircularDependencyThrowsException(string $class)
    {
        $this->expectException(Exception\CircularDependencyException::class);
        (new Injector())->create($class);
    }

    public function testSimpleTreeResolving()
    {
        /** @var TreeTestAsset\Simple $result */
        $result = (new Injector())->create(TreeTestAsset\Simple::class);
        $this->assertInstanceOf(TreeTestAsset\Simple::class, $result);
        $this->assertInstanceOf(TreeTestAsset\Level1::class, $result->result);
        $this->assertInstanceOf(TreeTestAsset\Level2::class, $result->result->result);
    }

    public function testComplexTreeResolving()
    {
        /** @var TreeTestAsset\Complex $result */
        $result = (new Injector())->create(TreeTestAsset\Complex::class);
        $this->assertInstanceOf(TreeTestAsset\Complex::class, $result);
        $this->assertInstanceOf(TreeTestAsset\Level1::class, $result->result);
        $this->assertInstanceOf(TreeTestAsset\Level2::class, $result->result->result);
        $this->assertInstanceOf(TreeTestAsset\AdditionalLevel1::class, $result->result2);
        $this->assertInstanceOf(TreeTestAsset\Level2::class, $result->result2->result);
        $this->assertSame($result->result->result, $result->result2->result);
    }

    public function testDeepDependencyUsesContainer()
    {
        $injector  = new Injector();
        $container = $this->getMockForAbstractClass(ContainerInterface::class);

        // Mocks a container that always creates new instances
        $container->method('has')->willReturnCallback(fn($class) => $injector->canCreate($class));
        $container->method('get')->willReturnCallback(fn($class) => $injector->create($class));

        $injector->setContainer($container);

        /** @var TreeTestAsset\Complex $result1 */
        $result1 = $injector->create(TreeTestAsset\Complex::class);
        /** @var TreeTestAsset\Complex $result2 */
        $result2 = $injector->create(TreeTestAsset\Complex::class);

        /** @var TreeTestAsset\Complex $result */
        foreach ([$result1, $result2] as $result) {
            $this->assertInstanceOf(TreeTestAsset\Complex::class, $result);
            $this->assertInstanceOf(TreeTestAsset\Level1::class, $result->result);
            $this->assertInstanceOf(TreeTestAsset\Level2::class, $result->result->result);
            $this->assertInstanceOf(TreeTestAsset\AdditionalLevel1::class, $result->result2);
            $this->assertInstanceOf(TreeTestAsset\Level2::class, $result->result2->result);
        }

        $this->assertNotSame($result1, $result2);
        $this->assertNotSame($result1->result, $result2->result);
        $this->assertNotSame($result1->result2, $result2->result2);
        $this->assertNotSame($result1->result->result, $result2->result->result);
        $this->assertNotSame($result1->result2->result, $result2->result2->result);

        $this->assertNotSame($result1->result->result, $result1->result2->result);
        $this->assertNotSame($result2->result->result, $result2->result2->result);
    }

    public function testDeepDependencyRespectsGlobalTypePreference()
    {
        $config = new Config([
            'preferences' => [
                TreeTestAsset\Level2::class => TreeTestAsset\Level2Preference::class,
            ],
        ]);

        /** @var TreeTestAsset\Complex $result */
        $result = (new Injector($config))->create(TreeTestAsset\Complex::class);
        $this->assertInstanceOf(TreeTestAsset\Level2Preference::class, $result->result2->result);
        $this->assertInstanceOf(TreeTestAsset\Level2Preference::class, $result->result->result);
    }

    public function testDeepDependencyRespectsSpecificTypePreference()
    {
        $config = new Config([
            'types' => [
                TreeTestAsset\AdditionalLevel1::class => [
                    'preferences' => [
                        TreeTestAsset\Level2::class => TreeTestAsset\Level2Preference::class,
                    ],
                ],
            ],
        ]);

        /** @var TreeTestAsset\Complex $result */
        $result = (new Injector($config))->create(TreeTestAsset\Complex::class);
        $this->assertInstanceOf(TreeTestAsset\Level2Preference::class, $result->result2->result);
        $this->assertNotInstanceOf(TreeTestAsset\Level2Preference::class, $result->result->result);
    }

    public function testDeepDependencyUsesConfiguredParameters()
    {
        $expected = uniqid('InjectValue');
        $config   = new Config([
            'types' => [
                TreeTestAsset\Level2::class => [
                    'parameters' => [
                        'opt' => $expected,
                    ],
                ],
            ],
        ]);

        /** @var TreeTestAsset\Complex $result */
        $result = (new Injector($config))->create(TreeTestAsset\Complex::class);
        $this->assertSame($expected, $result->result2->result->optionalResult);
        $this->assertSame($expected, $result->result->result->optionalResult);
    }

    public function testComplexDeepDependencyConfiguration()
    {
        $expected1 = uniqid('InjectValueA');
        $expected2 = uniqid('InjectValueB');

        $config = new Config([
            'types' => [
                TreeTestAsset\Level2::class           => [
                    'parameters' => [
                        'opt' => $expected1,
                    ],
                ],
                'Level2.Alias'                        => [
                    'typeOf'     => TreeTestAsset\Level2::class,
                    'parameters' => [
                        'opt' => $expected2,
                    ],
                ],
                TreeTestAsset\AdditionalLevel1::class => [
                    'preferences' => [
                        TreeTestAsset\Level2::class => 'Level2.Alias',
                    ],
                ],
            ],
        ]);

        /** @var TreeTestAsset\Complex $result */
        $result = (new Injector($config))->create(TreeTestAsset\Complex::class);
        $this->assertSame($expected1, $result->result->result->optionalResult);
        $this->assertSame($expected2, $result->result2->result->optionalResult);
    }

    public function testCreateInstanceWithoutUnknownClassThrowsException()
    {
        $this->expectException(Exception\ClassNotFoundException::class);
        (new Injector())->create('Unknown.Alias.Should.Fail');
    }

    public function testKnownButInexistentClassThrowsException()
    {
        $definition = $this->getMockBuilder(DefinitionInterface::class)
            ->getMockForAbstractClass();

        $definition->expects($this->any())
            ->method('hasClass')
            ->willReturn(true);

        $this->expectException(Exception\ClassNotFoundException::class);
        (new Injector(null, null, $definition))->create('LaminasTest\Di\TestAsset\No\Such\Class');
    }

    public function provideUnexpectedResolverValues(): array
    {
        return [
            'string' => ['string value'],
            'bool'   => [true],
            'null'   => [null],
            'object' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideUnexpectedResolverValues
     * @param mixed $unexpectedValue
     */
    public function testUnexpectedResolverResultThrowsTypeError($unexpectedValue)
    {
        $resolver = $this->getMockBuilder(DependencyResolverInterface::class)->getMockForAbstractClass();
        $resolver->expects($this->atLeastOnce())
            ->method('resolveParameters')
            ->willReturn([$unexpectedValue]);

        $this->expectException(TypeError::class);

        $injector = new Injector(null, null, null, $resolver);
        $injector->create(TestAsset\TypelessDependency::class);
    }

    public function provideContainerTypeNames(): array
    {
        return [
            'psr'     => [ContainerInterface::class],
            'interop' => ['Interop\Container\ContainerInterface'], // phpcs:ignore
        ];
    }

    /**
     * @dataProvider provideContainerTypeNames
     */
    public function testContainerItselfIsInjectedIfHasReturnsFalse(string $typeName)
    {
        $resolver  = $this->getMockBuilder(DependencyResolverInterface::class)->getMockForAbstractClass();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $resolver->expects($this->atLeastOnce())
            ->method('resolveParameters')
            ->willReturn([new TypeInjection($typeName)]);

        $container->method('has')->willReturn(false);

        $injector = new Injector(null, $container, null, $resolver);
        $result   = $injector->create(TestAsset\TypelessDependency::class);

        $this->assertInstanceOf(TestAsset\TypelessDependency::class, $result);
        $this->assertSame($container, $result->result);
    }

    public function testTypeUnavailableInContainerThrowsException()
    {
        $expectedMessage = 'Exception from container';
        $resolver        = $this->getMockBuilder(DependencyResolverInterface::class)->getMockForAbstractClass();
        $container       = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
        $resolver->expects($this->atLeastOnce())
            ->method('resolveParameters')
            ->willReturn([new TypeInjection(TestAsset\A::class)]);

        $container->method('has')
            ->willReturn(false);

        $container->method('get')
            ->willThrowException(
                new class ($expectedMessage) extends RuntimeException implements NotFoundExceptionInterface {
                }
            );

        $this->expectException(Exception\UndefinedReferenceException::class);
        $this->expectExceptionMessage($expectedMessage);

        $injector = new Injector(null, $container, null, $resolver);
        $injector->create(TestAsset\TypelessDependency::class);
    }

    public function provideManyArguments(): array
    {
        return [
            'three' => [
                TestAsset\Constructor\ThreeArguments::class,
                [
                    'a' => 'a',
                    'b' => 'something',
                    'c' => true,
                ],
            ],
            'six'   => [
                TestAsset\Constructor\ManyArguments::class,
                [
                    'a' => 'a',
                    'b' => 'something',
                    'c' => true,
                    'd' => 8,
                    'e' => new stdClass(),
                    'f' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideManyArguments
     */
    public function testConstructionWithManyParameters(string $class, array $parameters)
    {
        $result = (new Injector())->create($class, $parameters);
        $this->assertEquals($parameters, $result->result);
    }

    public function testCreateGivenExistingInterfaceExpectedClassNotFoundExceptionThrown()
    {
        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->method('hasClass')
            ->willReturn(true);

        $injector = new Injector(null, null, $definition);

        $this->expectException(ClassNotFoundException::class);
        $injector->create(DefinitionInterface::class);
    }
}
