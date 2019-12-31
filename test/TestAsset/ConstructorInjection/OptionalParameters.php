<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\ConstructorInjection;

/**
 * Test asset used to verify that default parameters in __construct are used correctly
 */
class OptionalParameters
{
    /**
     * @var mixed
     */
    public $a = 'default';

    /**
     * @var mixed
     */
    public $b = 'default';

    /**
     * @var mixed
     */
    public $c = 'default';

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     */
    public function __construct($a = null, $b = 'defaultConstruct', $c = array())
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
}
