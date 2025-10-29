<?php

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

/**
 * @final This class should not be extended and will be marked final in version 4.0
 */
class AdditionalLevel1
{
    /** @var Level2 */
    public $result;

    public function __construct(Level2 $dep)
    {
        $this->result = $dep;
    }
}
