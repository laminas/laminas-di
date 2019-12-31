<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset;

use LaminasTest\Di\TestAsset\AggregateClasses\ItemInterface;

class AggregatedParamClass
{
    public $aggregator = null;

    public function __construct(ItemInterface $item)
    {
        $this->aggregator = $item;
    }
}
