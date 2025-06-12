<?php

declare(strict_types=1);

namespace LaminasTest\Di\Config;

use Laminas\Di\Config\Parameter;
use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class ParameterTest extends TestCase
{
    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function provideImplicitValueInjectionTestData(): iterable
    {
        return [
            'bool'   => ['A', true],
            'int'    => ['A', 12],
            'object' => ['A', new stdClass()],
        ];
    }

    /**
     * @dataProvider provideImplicitValueInjectionTestData
     */
    public function testShouldBuildImplicitValueInjectionFromConfig(string $name, mixed $value): void
    {
        $parameter     = Parameter::fromValue($name, $value);
        $containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();

        self::assertSame($name, $parameter->name);
        self::assertInstanceOf(ValueInjection::class, $parameter->injection);
        self::assertSame($value, $parameter->injection->toValue($containerMock));
    }

    /**
     * @return iterable<string, array{string, string|InjectionInterface}>
     */
    public static function provideExplicitInjectionTestData(): iterable
    {
        return [
            'string'         => ['A', 'SomeService'],
            'wildcard'       => ['A', '*'],
            'TypeInjection'  => ['A', new TypeInjection('SomeService')],
            'ValueInjection' => ['A', new ValueInjection('SomeValue')],
        ];
    }

    /**
     * @dataProvider provideExplicitInjectionTestData
     */
    public function testShouldKeepExplicitInjectionInfoFromConfig(string $name, string|InjectionInterface $value): void
    {
        $parameter = Parameter::fromValue($name, $value);

        self::assertSame($name, $parameter->name);
        self::assertSame($value, $parameter->injection);
    }
}
