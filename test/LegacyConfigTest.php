<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di;

use ArrayIterator;
use GlobIterator;
use Laminas\Di\Exception;
use Laminas\Di\LegacyConfig;
use PHPUnit\Framework\Error\Deprecated as DeprecatedError;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use stdClass;

/**
 * @coversDefaultClass Laminas\Di\LegacyConfig
 */
class LegacyConfigTest extends TestCase
{
    public function provideMigrationConfigFixtures(): array
    {
        $iterator = new GlobIterator(__DIR__ . '/_files/legacy-configs/*.php');
        $values   = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $key  = $file->getBasename('.php');
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
    public function testLegacyConfigMigration(array $config, array $expected)
    {
        $instance = new LegacyConfig($config);
        $this->assertEquals($expected, $instance->toArray());
    }

    public function testFQParamNamesTriggerDeprecated()
    {
        $this->expectDeprecation(DeprecatedError::class);

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

    public function testConstructWithTraversable()
    {
        $spec     = include __DIR__ . '/_files/legacy-configs/common.php';
        $config   = new ArrayIterator($spec['config']);
        $instance = new LegacyConfig($config);

        $this->assertEquals($spec['expected'], $instance->toArray());
    }

    public function testConstructWithInvalidConfigThrowsException()
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        new LegacyConfig(new stdClass());
    }
}
