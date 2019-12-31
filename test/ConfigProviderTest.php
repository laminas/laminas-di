<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\ConfigProvider;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Laminas\Di\Module
 */
class ConfigProviderTest extends TestCase
{
    public function testInstanceIsInvokable()
    {
        $this->assertInternalType(IsType::TYPE_CALLABLE, new ConfigProvider());
    }

    public function testProvidesDependencies()
    {
        $provider = new ConfigProvider();
        $result = $provider();

        $this->assertArrayHasKey('dependencies', $result);
        $this->assertEquals($provider->getDependencyConfig(), $result['dependencies']);
    }
}
