<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use ArrayIterator;
use GlobIterator;
use Laminas\Di\Exception;
use Laminas\Di\LegacyConfig;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use stdClass;

use function restore_error_handler;
use function set_error_handler;

use const E_USER_DEPRECATED;

/**
 * @coversDefaultClass Laminas\Di\LegacyConfig
 */
final class LegacyConfigTest extends TestCase
{
    /**
     * @return array<string, array{0: array, 1: array}>
     */
    public function provideMigrationConfigFixtures(): array
    {
        $iterator = new GlobIterator(__DIR__ . '/_files/legacy-configs/*.php');
        $values   = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $key = $file->getBasename('.php');

            /** @var array{config: array, expected: array} $data */
            $data = include $file->getPathname();

            $values[$key] = [
                $data['config'],
                $data['expected'],
            ];
        }

        return $values;
    }

    /**
     * @dataProvider provideMigrationConfigFixtures
     */
    public function testLegacyConfigMigration(array $config, array $expected): void
    {
        $instance = new LegacyConfig($config);
        $this->assertEquals($expected, $instance->toArray());
    }

    public function testFQParamNamesTriggerDeprecated(): void
    {
        $expectedMessage = 'Full qualified parameter positions are no longer supported';

        set_error_handler(function ($errno, $errstr) use ($expectedMessage) {
            if ($errno === E_USER_DEPRECATED) {
                $this->assertStringContainsString($expectedMessage, $errstr);
                return true;
            }
            return false;
        }, E_USER_DEPRECATED);

        try {
            new LegacyConfig([
                'instance' => [
                    'FooClass' => [
                        'parameters' => [
                            'BarClass:__construct:0' => 'Value for fq param name',
                        ],
                    ],
                ],
            ]);
        } finally {
            restore_error_handler();
        }
    }

    public function testConstructWithTraversable(): void
    {
        /** @var array{config: array, expected: array} $spec */
        $spec     = include __DIR__ . '/_files/legacy-configs/common.php';
        $config   = new ArrayIterator($spec['config']);
        $instance = new LegacyConfig($config);

        $this->assertEquals($spec['expected'], $instance->toArray());
    }

    public function testConstructWithInvalidConfigThrowsException(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        new LegacyConfig(new stdClass());
    }
}
