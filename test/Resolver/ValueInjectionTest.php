<?php

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Exception;
use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\ValueInjection;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerInterface;
use stdClass;

use function fclose;
use function fopen;
use function microtime;
use function restore_error_handler;
use function set_error_handler;
use function time;
use function uniqid;

use const E_USER_DEPRECATED;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ValueInjection::class)]
final class ValueInjectionTest extends TestCase
{
    /** @var false|resource */
    private $streamFixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->streamFixture) {
            $this->streamFixture = fopen('php://temp', 'w+');
        }
    }

    protected function tearDown(): void
    {
        if ($this->streamFixture) {
            fclose($this->streamFixture);
            $this->streamFixture = null;
        }

        parent::tearDown();
    }

    public function testImplementsContract()
    {
        $this->assertInstanceOf(InjectionInterface::class, new ValueInjection(null));
    }

    public static function provideConstructionValues(): array
    {
        return [
            'string' => ['Hello World'],
            'bool'   => [true],
            'int'    => [7_364_234],
            'object' => [new stdClass()],
            'null'   => [null],
        ];
    }

    /**
     */
    #[DataProvider('provideConstructionValues')]
    public function testSetStateConstructsInstance(mixed $value)
    {
        $container = $this->createMock(ContainerInterface::class);
        $result    = ValueInjection::__set_state(['value' => $value]);
        $this->assertInstanceOf(ValueInjection::class, $result);
        $this->assertSame($value, $result->toValue($container));
    }

    /**
     */
    #[DataProvider('provideConstructionValues')]
    public function testToValueBypassesContainer(mixed $value)
    {
        $result    = new ValueInjection($value);
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects(self::never())
            ->method('get');

        $this->assertSame($value, $result->toValue($container));
    }

    public static function provideExportableValues(): array
    {
        return [
            'string'       => ['Testvalue'],
            'int'          => [124342],
            'randomString' => [uniqid()],
            'time'         => [time()],
            'true'         => [true],
            'false'        => [false],
            'null'         => [null],
            'float'        => [microtime(true)],
            'object'       => [new TestAsset\Resolver\ExportableValue()],
            'array'        => [[]],
            'array-string' => [['TestValue', 'OtherValue']],
            'array-int'    => [[123, 456]],
            'array-mixed'  => [
                [
                    new TestAsset\Resolver\ExportableValue(),
                    [1],
                    null,
                    false,
                    true,
                    time(),
                    microtime(true),
                    [[], []],
                    uniqid(),
                    [],
                ],
            ],
        ];
    }

    public static function provideUnexportableItems(): array
    {
        $streamFixture = fopen('php://temp', 'w+');

        return [
            'stream'          => [$streamFixture],
            'noSetState'      => [new TestAsset\Resolver\UnexportableValue1()],
            'arrayNoSetState' => [[new TestAsset\Resolver\UnexportableValue1()]],
        ];
    }

    /**
     */
    #[DataProvider('provideUnexportableItems')]
    public function testExportThrowsExceptionForUnexportable(mixed $value)
    {
        $instance = new ValueInjection($value);

        $this->expectException(Exception\LogicException::class);
        $instance->export();
    }

    /**
     */
    #[DataProvider('provideUnexportableItems')]
    public function testIsExportableReturnsFalseForUnexportable(mixed $value)
    {
        $instance = new ValueInjection($value);
        $this->assertFalse($instance->isExportable());
    }

    /**
     */
    #[DataProvider('provideExportableValues')]
    public function testIsExportableReturnsTrueForExportableValues(mixed $value)
    {
        $instance = new ValueInjection($value);
        $this->assertTrue($instance->isExportable());
    }

    /**
     */
    #[DataProvider('provideExportableValues')]
    public function testExportWithExportableValues(mixed $value)
    {
        $instance = new ValueInjection($value);
        $result   = $instance->export();

        $this->assertIsString($result, 'Export is expected to return a string value');
        $this->assertNotEquals('', $result, 'The exported value must not be empty');
    }

    public function testGetValueTriggersDeprecatedNotice()
    {
        $value   = uniqid();
        $subject = new ValueInjection($value);

        $expectedMessage = 'ValueInjection::getValue is deprecated';

        set_error_handler(function ($errno, $errstr) use ($expectedMessage) {
            if ($errno === E_USER_DEPRECATED) {
                $this->assertStringContainsString($expectedMessage, $errstr);
                return true;
            }
            return false;
        }, E_USER_DEPRECATED);

        try {
            self::assertSame($value, $subject->getValue());
        } finally {
            restore_error_handler();
        }
    }
}
