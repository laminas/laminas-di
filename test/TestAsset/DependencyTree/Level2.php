<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

class Level2
{
    /** @var mixed */
    public $optionalResult;

    /** @param null|mixed $opt*/
    public function __construct($opt = null)
    {
        $this->optionalResult = $opt;
    }
}
