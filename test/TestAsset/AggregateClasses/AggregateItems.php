<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\TestAsset\AggregateClasses;

class AggregateItems implements ItemInterface
{
    public $items = array();

    public function addItem(ItemInterface $item)
    {
        $this->items[] = $item;
        return $this;
    }
}
