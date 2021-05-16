<?php

declare(strict_types=1);

namespace LaminasTest\Di;

use Laminas\Di\ConfigProvider;
use Laminas\Di\Module;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Laminas\Di\Module
 */
class ModuleTest extends TestCase
{
    public function testModuleProvidesServiceConfiguration(): void
    {
        $module         = new Module();
        $configProvider = new ConfigProvider();

        $config = $module->getConfig();
        $this->assertArrayHasKey('service_manager', $config);
        $this->assertEquals($configProvider->getDependencyConfig(), $config['service_manager']);
    }
}
