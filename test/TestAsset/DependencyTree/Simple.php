<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

class Simple
{
    /** @var Level1 */
    public $result;

    public function __construct(Level1 $dep)
    {
        $this->result = $dep;
    }
}
