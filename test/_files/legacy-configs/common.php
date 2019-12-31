<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di;

return [
    'config' => [
        // This should be silently ignored
        'definition' => [
            'compiler' => [],
            'runtime'  => [],
        ],

        'instance' => [
            'aliases' => [
                'A.Alias' => TestAsset\A::class,
                'B.Alias' => TestAsset\B::class,
            ],
            'preferences' => [
                TestAsset\DummyInterface::class => [
                    TestAsset\B::class,
                    TestAsset\A::class,
                ],
                TestAsset\B::class => TestAsset\ExtendedB::class,
            ],
            TestAsset\ExtendedB::class => [
                'shared' => true,
                'parameters' => [
                    'a' => TestAsset\A::class,
                    'b' => 'String value for "b"',
                ],
            ],
        ],
    ],
    'expected' => [
        'preferences' => [
            TestAsset\DummyInterface::class => TestAsset\A::class,
            TestAsset\B::class => TestAsset\ExtendedB::class,
        ],
        'types' => [
            'A.Alias' => [
                'typeOf' => TestAsset\A::class,
            ],
            'B.Alias' => [
                'typeOf' => TestAsset\B::class,
            ],
            TestAsset\ExtendedB::class => [
                'parameters' => [
                    'a' => TestAsset\A::class,
                    'b' => 'String value for "b"',
                ],
            ],
        ],
    ],
];
