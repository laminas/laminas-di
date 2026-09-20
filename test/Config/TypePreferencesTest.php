<?php

declare(strict_types=1);

namespace LaminasTest\Di\Config;

use Laminas\Di\Config\Exception\InvalidTypePreferenceException;
use Laminas\Di\Config\TypePreferences;
use PHPUnit\Framework\TestCase;
use Stringable;

class TypePreferencesTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function provideInvalidConfigValues(): iterable
    {
        return [
            'non-array'   => ['Test'],
            'int-value'   => [
                ['Test' => 1234],
            ],
            'array-value' => [
                ['Test' => ['a', 'b']],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidConfigValues
     */
    public function testShouldThrowOnInvalidConfigValue(mixed $value): void
    {
        $this->expectException(InvalidTypePreferenceException::class);
        TypePreferences::fromConfigValue($value);
    }

    public function testShouldCreateFromConfigValue(): void
    {
        $c = new class () implements Stringable {
            public function __toString(): string
            {
                return 'c';
            }
        };

        $config = TypePreferences::fromConfigValue([
            'a' => 'b',
            'b' => $c,
        ]);

        self::assertEquals(
            [
                'a' => 'b',
                'b' => 'c',
            ],
            $config->preferences,
        );
    }
}
