<?php

declare(strict_types=1);

namespace LaminasTest\Di\Config;

use Laminas\Di\Config\Exception\InvalidParametersException;
use Laminas\Di\Config\Parameter;
use Laminas\Di\Config\ParameterMap;
use Laminas\Di\Resolver\TypeInjection;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_keys;

class ParameterMapTest extends TestCase
{
    public function testShouldConstructFromConfigArray(): void
    {
        $explicitParam = new Parameter('explicitParam', 'Type');
        $input         = [
            'implicitType'  => 'Type',
            'autowired'     => '*',
            'valueArray'    => [],
            'valueObject'   => new stdClass(),
            'explicitValue' => new ValueInjection('value'),
            'explicitType'  => new TypeInjection('Type'),
            $explicitParam,
        ];
        $expectedKeys  = [
            'implicitType',
            'autowired',
            'valueArray',
            'valueObject',
            'explicitValue',
            'explicitType',
            'explicitParam',
        ];

        $parameterMap = ParameterMap::fromConfigValue($input);

        self::assertEquals($expectedKeys, array_keys($parameterMap->toArray()));

        foreach ($expectedKeys as $key) {
            self::assertSame($key, $parameterMap->get($key)?->name);
        }

        self::assertSame('Type', $parameterMap->get('implicitType')?->injection);
        self::assertSame('*', $parameterMap->get('autowired')?->injection);
        self::assertSame($input['explicitValue'], $parameterMap->get('explicitValue')?->injection);
        self::assertSame($input['explicitType'], $parameterMap->get('explicitType')?->injection);
        self::assertSame($explicitParam, $parameterMap->get('explicitParam'));
        self::assertEquals(new ValueInjection([]), $parameterMap->get('valueArray')?->injection);
        self::assertEquals(new ValueInjection(new stdClass()), $parameterMap->get('valueObject')?->injection);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideInvalidConfigValuesTestData(): iterable
    {
        return [
            'non-iterable' => ['string'],
            'numeric-key'  => [
                [1 => 'ImplicitType'],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidConfigValuesTestData
     */
    public function testShouldThrowOnInvalidConfigValue(mixed $input): void
    {
        $this->expectException(InvalidParametersException::class);
        ParameterMap::fromConfigValue($input);
    }
}
