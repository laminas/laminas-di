<?php

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Exception;
use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\ValueInjection;
use LaminasTest\Di\TestAsset;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

use function fclose;
use function fopen;
use function microtime;
use function time;
use function uniqid;

/** @covers \Laminas\Di\Resolver\ValueInjection */
class ValueInjectionTest extends TestCase
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

    public function provideConstructionValues(): array
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
     * @dataProvider provideConstructionValues
     * @param mixed $value
     */
    public function testSetStateConstructsInstance($value)
    {
        $container = $this->createMock(ContainerInterface::class);
        $result    = ValueInjection::__set_state(['value' => $value]);
        $this->assertInstanceOf(ValueInjection::class, $result);
        $this->assertSame($value, $result->toValue($container));
    }

    /**
     * @dataProvider provideConstructionValues
     * @param mixed $value
     */
    public function testToValueBypassesContainer($value)
    {
        $result    = new ValueInjection($value);
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects(self::never())
            ->method('get');

        $this->assertSame($value, $result->toValue($container));
    }

    public function provideExportableValues(): array
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

    public function provideUnexportableItems(): array
    {
        if (! $this->streamFixture) {
            $this->streamFixture = fopen('php://temp', 'w+');
        }

        return [
            'stream'          => [$this->streamFixture],
            'noSetState'      => [new TestAsset\Resolver\UnexportableValue1()],
            'arrayNoSetState' => [[new TestAsset\Resolver\UnexportableValue1()]],
        ];
    }

    /**
     * @dataProvider provideUnexportableItems
     * @param mixed $value
     */
    public function testExportThrowsExceptionForUnexportable($value)
    {
        $instance = new ValueInjection($value);

        $this->expectException(Exception\LogicException::class);
        $instance->export();
    }

    /**
     * @dataProvider provideUnexportableItems
     * @param mixed $value
     */
    public function testIsExportableReturnsFalseForUnexportable($value)
    {
        $instance = new ValueInjection($value);
        $this->assertFalse($instance->isExportable());
    }

    /**
     * @dataProvider provideExportableValues
     * @param mixed $value
     */
    public function testIsExportableReturnsTrueForExportableValues($value)
    {
        $instance = new ValueInjection($value);
        $this->assertTrue($instance->isExportable());
    }

    /**
     * @dataProvider provideExportableValues
     * @param mixed $value
     */
    public function testExportWithExportableValues($value)
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

        $this->expectDeprecation();
        self::assertSame($value, $subject->getValue());
    }
}
