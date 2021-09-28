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

/**
 * @coversDefaultClass Laminas\Di\LegacyConfig
 */
class LegacyConfigTest extends TestCase
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
        $this->expectDeprecation();

        new LegacyConfig([
            'instance' => [
                'FooClass' => [
                    'parameters' => [
                        'BarClass:__construct:0' => 'Value for fq param name',
                    ],
                ],
            ],
        ]);
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
