<?php

declare(strict_types=1);

namespace LaminasTest\Di\Classes;

use Countable;
use Iterator;

class Bar
{
    public function __construct(Iterator&Countable $iter)
    {
    }
}
