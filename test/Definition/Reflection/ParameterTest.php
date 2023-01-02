<?php

declare(strict_types=1);

namespace LaminasTest\Di\Definition\Reflection;

use Laminas\Di\Definition\Reflection\Parameter;
use Laminas\Di\Exception\UnsupportedReflectionTypeException;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

use function assert;

/**
 * Parameter test case.
 */
class ParameterTest extends TestCase
{
    use ParameterTestTrait;

    public function provideGeneralParameters(): array
    {
        $params = (new ReflectionClass(TestAsset\Parameters::class))->getMethod('general')->getParameters();

        return [
            //             [$param,    name, pos,  req?, default]
            'notype'    => [$params[0], 'a',   0,  true, null],
            'classhint' => [$params[1], 'b',   1,  true, null],
            'optional'  => [$params[2], 'c',   2, false, 'something'],
        ];
    }

    /**
     * @dataProvider provideGeneralParameters
     */
    public function testParamterReflectedCorrectly(
        ReflectionParameter $reflection,
        string $expectedName,
        int $expectedPosition,
        bool $expectRequired,
        mixed $expectedDefault
    ) {
        $instance = new Parameter($reflection);

        $this->assertSame($expectedName, $instance->getName());
        $this->assertSame($expectedPosition, $instance->getPosition());

        if ($expectRequired) {
            $this->assertTrue($instance->isRequired(), 'Parameter is expected to be required');
        } else {
            $this->assertFalse($instance->isRequired(), 'Param is not expected to be required');
            $this->assertSame($expectedDefault, $instance->getDefault());
        }
    }

    /**
     * @dataProvider provideTypehintedParameterReflections
     */
    public function testTypehintedParameter(ReflectionParameter $reflection, ?string $expectedType)
    {
        $required = new Parameter($reflection);
        $this->assertSame($expectedType, $required->getType());
        $this->assertFalse($required->isBuiltin());
    }

    /**
     * @dataProvider provideTypelessParameterReflections
     */
    public function testTypelessParamter(ReflectionParameter $reflection)
    {
        $param = new Parameter($reflection);
        $this->assertNull($param->getType(), 'Parameter type must be null');
        $this->assertFalse($param->isBuiltin(), 'Parameter must not be exposed builtin');
    }

    public function provideScalarTypehintedReflections(): array
    {
        return $this->buildReflectionArgsFromClass(TestAsset\ScalarTypehintParameters::class);
    }

    /**
     * @dataProvider provideBuiltinTypehintedReflections
     */
    public function testBuiltinTypehintedParameters(ReflectionParameter $reflection, string $expectedType)
    {
        $param = new Parameter($reflection);
        $this->assertTrue($param->isBuiltin());
        $this->assertSame($expectedType, $param->getType());
    }

    /**
     * @dataProvider provideScalarTypehintedReflections
     */
    public function testScalarTypehintedParameters(ReflectionParameter $reflection, string $expectedType)
    {
        $param = new Parameter($reflection);
        $this->assertTrue($param->isBuiltin());
        $this->assertSame($expectedType, $param->getType());
    }

    public function testIterablePseudoType()
    {
        $reflections = $this->getConstructor(TestAsset\IterableDependency::class)->getParameters();
        $param       = new Parameter($reflections[0]);

        $this->assertTrue($param->isBuiltin());
        $this->assertSame('iterable', $param->getType());
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testIsBuiltinGivenUnionTypeExpectedUnsupportedReflectionTypeExceptionThrown(): void
    {
        $this->expectException(UnsupportedReflectionTypeException::class);

        $class      = TestAsset\Constructor\UnionTypeConstructorDependency::class;
        $parameters = $this->getConstructor($class)->getParameters();
        $param      = new Parameter($parameters[0]);

        $param->isBuiltin();
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testIsBuiltinGivenIntersectionTypeExpectedUnsupportedReflectionTypeExceptionThrown(): void
    {
        $this->expectException(UnsupportedReflectionTypeException::class);

        /** @var class-string $class this is written as is due to CS/SA tooling not being PHP-8.1-friendly yet */
        $class      = 'LaminasTest\Di\TestAsset\Constructor\\' . 'IntersectionTypeConstructorDependency'; // phpcs:ignore
        $parameters = $this->getConstructor($class)->getParameters();
        $param      = new Parameter($parameters[0]);

        $param->isBuiltin();
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testGetTypeGivenUnionTypeExpectedUnsupportedReflectionTypeExceptionThrown(): void
    {
        $this->expectException(UnsupportedReflectionTypeException::class);

        $class      = TestAsset\Constructor\UnionTypeConstructorDependency::class;
        $parameters = $this->getConstructor($class)->getParameters();
        $param      = new Parameter($parameters[0]);

        $param->getType();
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testGetTypeGivenIntersectionTypeExpectedUnsupportedReflectionTypeExceptionThrown(): void
    {
        $this->expectException(UnsupportedReflectionTypeException::class);

        /** @var class-string $class this is written as is due to CS/SA tooling not being PHP-8.1-friendly yet */
        $class      = 'LaminasTest\Di\TestAsset\Constructor\\' . 'IntersectionTypeConstructorDependency'; // phpcs:ignore
        $parameters = $this->getConstructor($class)->getParameters();
        $param      = new Parameter($parameters[0]);

        $param->getType();
    }

    /** @param class-string $class */
    private function getConstructor(string $class): ReflectionMethod
    {
        $constructor = (new ReflectionClass($class))->getConstructor();

        assert($constructor !== null);

        return $constructor;
    }
}
