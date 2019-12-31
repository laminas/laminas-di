<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

return [
    'preferences' => [
        'A' => 'GlobalA',
        'B' => 'GlobalB'
    ],
    'types' => [
        'Foo' => [
            'preferences' => [
                'A' => 'LocalA',
             ],
            'parameters' => [
                'a' => '*'
            ]
        ],
        'Bar' => [
            'typeOf' => 'Foo',
            'preferences' => [
                'B' => 'LocalB'
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
