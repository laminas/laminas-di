<?php

// @phpcs:disable

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\Constructor;

use function array_filter;
use function compact;

class ManyArguments
{
    public $result;

    public function __construct(
        $a = null,
        $b = null,
        $c = null,
        $d = null,
        $e = null,
        $f = null
    ) {
        $this->result = array_filter(compact('a', 'b', 'c', 'd', 'e', 'f'), function ($value) {
            return $value !== null;
        });
    }
}
