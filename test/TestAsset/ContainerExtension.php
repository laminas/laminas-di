<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

use Laminas\Di\ServiceLocator;

class ContainerExtension extends ServiceLocator
{
    public $foo;
    public $params;

    protected $map = array(
        'foo'    => 'getFoo',
        'params' => 'getParams',
    );

    public function getFoo()
    {
        return $this->foo;
    }

    public function getParams(array $params)
    {
        $this->params = $params;
        return $this->params;
    }
}
