<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

class Level1
{
    /** @var Level2 */
    public $result;

    public function __construct(Level2 $dep)
    {
        $this->result = $dep;
    }
}
