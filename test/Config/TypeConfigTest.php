<?php

declare(strict_types=1);

namespace LaminasTest\Di\Config;

use Laminas\Di\Config\AliasConfig;
use Laminas\Di\Config\Exception\InvalidConfigException;
use Laminas\Di\Config\Exception\UndefinedClassException;
use Laminas\Di\Config\Parameter;
use Laminas\Di\Config\ParameterMap;
use Laminas\Di\Config\TypeConfig;
use Laminas\Di\Config\TypePreferences;
use Laminas\Di\Injector;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use stdClass;
use Traversable;

class TypeConfigTest extends TestCase
{
    public function testShouldConstructTypeMapFromConfigValue(): void
    {
        $config = TypeConfig::mapFromConfigValue([
            'TypeConfig'  => [
                'preferences' => [
                    'Type' => 'PreferredType',
                ],
                'parameters'  => [
                    'implicitType'  => 'Type',
                    'implicitValue' => true,
                ],
            ],
            'AliasConfig' => [
                'typeOf'      => 'SomeClass',
                'preferences' => [
                    'Type' => 'OtherType',
                ],
                'parameters'  => [
                    'implicitType'  => 'OtherInjectedType',
                    'implicitValue' => 17,
                ],
            ],
        ]);

        $this->assertEquals(
            [
                'TypeConfig'  => new TypeConfig(
                    'TypeConfig',
                    new TypePreferences([
                        'Type' => 'PreferredType',
                    ]),
                    new ParameterMap(
                        new Parameter('implicitType', 'Type'),
                        new Parameter('implicitValue', new ValueInjection(true)),
                    ),
                ),
                'AliasConfig' => new AliasConfig(
                    'AliasConfig',
                    new TypeConfig(
                        'SomeClass',
                        new TypePreferences([
                            'Type' => 'OtherType',
                        ]),
                        new ParameterMap(
                            new Parameter('implicitType', 'OtherInjectedType'),
                            new Parameter('implicitValue', new ValueInjection(17)),
                        ),
                    ),
                ),
            ],
            $config
        );
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideInvalidConfigInputTestData(): iterable
    {
        return [
            'non-iterable'     => [new stdClass()],
            'numeric type key' => [[1 => []]],
            'bad type config'  => [['TypeName' => new stdClass()]],
        ];
    }

    /**
     * @dataProvider provideInvalidConfigInputTestData
     */
    public function testShouldThrowOnInvalidConfigInput(mixed $input): void
    {
        $this->expectException(InvalidConfigException::class);
        TypeConfig::mapFromConfigValue($input);
    }

    public function testGetClassShouldThrowWhenClassDoesNotExist(): void
    {
        $config = new TypeConfig('UndefinedClass');

        $this->expectException(UndefinedClassException::class);
        $config->getClassName();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideExistingTypes(): iterable
    {
        $types = [
            Traversable::class,
            Injector::class,
        ];

        foreach ($types as $type) {
            yield $type => [$type];
        }
    }

    /**
     * @dataProvider provideExistingTypes
     */
    public function testGetClassShouldReturnExistingClasses(string $name): void
    {
        self::assertSame($name, (new TypeConfig($name))->getClassName());
    }
}
