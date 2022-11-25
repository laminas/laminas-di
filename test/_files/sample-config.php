<?php

use LaminasTest\Di\TestAsset;

return [
    'preferences' => [
        TestAsset\A::class => 'GlobalA',
        TestAsset\B::class => 'GlobalB',
    ],
    'types' => [
        TestAsset\Config\SomeClass::class => [
            'preferences' => [
                TestAsset\A::class => 'LocalA',
             ],
            'parameters' => [
                'a' => '*'
            ]
        ],
        'SomeAlias' => [
            'typeOf' => TestAsset\Config\SomeClass::class,
            'preferences' => [
                TestAsset\B::class => 'LocalB'
            ]
        ]
    ],

    'arbitaryKey' => 'value',
    'factories' => [
        'should be' => [
            'ignored' => 'as well'
        ]
    ]
];
