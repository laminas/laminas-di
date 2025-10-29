<?php

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Resolver\AbstractInjection;
use Laminas\Di\Resolver\InjectionInterface;
use PHPUnit\Framework\TestCase;

use function restore_error_handler;
use function set_error_handler;
use function sprintf;

use const E_USER_DEPRECATED;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class AbstractInjectionTest extends TestCase
{
    public function testUsageIsDeprecated(): void
    {
        $expectedMessage = sprintf(
            '%s is deprecated, please migrate to %s',
            AbstractInjection::class,
            InjectionInterface::class
        );

        set_error_handler(function ($errno, $errstr) use ($expectedMessage) {
            if ($errno === E_USER_DEPRECATED) {
                $this->assertStringContainsString($expectedMessage, $errstr);
                return true;
            }
            return false;
        }, E_USER_DEPRECATED);

        try {
            new class () extends AbstractInjection
            {
                public function export(): string
                {
                    return '';
                }

                public function isExportable(): bool
                {
                    return true;
                }
            };
        } finally {
            restore_error_handler();
        }
    }
}
