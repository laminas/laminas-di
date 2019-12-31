<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use Laminas\Di\InstanceManager;
use PHPUnit_Framework_TestCase as TestCase;

class InstanceManagerTest extends TestCase
{
    public function testInstanceManagerCanPersistInstances()
    {
        $im = new InstanceManager();
        $obj = new TestAsset\BasicClass();
        $im->addSharedInstance($obj, 'LaminasTest\Di\TestAsset\BasicClass');
        $this->assertTrue($im->hasSharedInstance('LaminasTest\Di\TestAsset\BasicClass'));
        $this->assertSame($obj, $im->getSharedInstance('LaminasTest\Di\TestAsset\BasicClass'));
    }

    public function testInstanceManagerCanPersistInstancesWithParameters()
    {
        $im = new InstanceManager();
        $obj1 = new TestAsset\BasicClass();
        $obj2 = new TestAsset\BasicClass();
        $obj3 = new TestAsset\BasicClass();

        $im->addSharedInstance($obj1, 'foo');
        $im->addSharedInstanceWithParameters($obj2, 'foo', ['foo' => 'bar']);
        $im->addSharedInstanceWithParameters($obj3, 'foo', ['foo' => 'baz']);

        $this->assertSame($obj1, $im->getSharedInstance('foo'));
        $this->assertSame($obj2, $im->getSharedInstanceWithParameters('foo', ['foo' => 'bar']));
        $this->assertSame($obj3, $im->getSharedInstanceWithParameters('foo', ['foo' => 'baz']));
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerCanResolveRecursiveAliases()
    {
        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'Some\Class');
        $im->addAlias('foo-alias', 'bar-alias');
        $class = $im->getClassFromAlias('foo-alias');
        $this->assertEquals('Some\Class', $class);
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerThrowsExceptionForRecursiveAliases()
    {
        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'foo-alias');
        $im->addAlias('foo-alias', 'bar-alias');

        $this->setExpectedException('Laminas\Di\Exception\RuntimeException', 'recursion');
        $im->getClassFromAlias('foo-alias');
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerResolvesRecursiveAliasesForConfig()
    {
        $config = ['parameters' => ['username' => 'my-username']];

        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'Some\Class');
        $im->addAlias('foo-alias', 'bar-alias');
        $im->setConfig('bar-alias', $config);

        $config['injections'] = [];
        $config['shared'] = true;

        $this->assertEquals($config, $im->getConfig('foo-alias'));
    }
}
