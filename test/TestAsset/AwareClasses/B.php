<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\AwareClasses;

class B implements NoParamsAwareInterface
{
    /**
     * @see \LaminasTest\Di\TestAsset\AwareClasses\NoParamsAwareInterface::getSomething()
     */
    public function getSomething()
    {
    }

    /**
     * @see \LaminasTest\Di\TestAsset\AwareClasses\NoParamsAwareInterface::setSomething()
     */
    public function setSomething(A $something)
    {
    }
}
