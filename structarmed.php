<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Preset\Presets\Psr1Preset;

return Architecture::define()
    ->skipPath(__DIR__ . '/test/_files/')
    ->skip([
        Psr1Preset::FILES_SHOULD_DECLARE_SYMBOLS_OR_SIDE_EFFECTS => [
            // skipped as a deprecated class
            __DIR__ . '/src/Resolver/AbstractInjection.php',
        ],
    ])
    ->withPresets(Preset::PER(), Preset::CODEQUALITY())
    ->layer('Di', 'src/', [
        'src/CodeGenerator/',
        'src/Container/',
        'src/Definition/',
        'src/Exception/',
        'src/Resolver/',
    ])
    ->layer('CodeGenerator', 'src/CodeGenerator/')
    ->layer('Container', 'src/Container/', 'src/Container/ServiceManager/')
    ->layer('ServiceManager', 'src/Container/ServiceManager/')
    ->layer('Definition', 'src/Definition/', 'src/Definition/Reflection/')
    ->layer('Reflection', 'src/Definition/Reflection/')
    ->layer('Exception', 'src/Exception/')
    ->layer('Resolver', 'src/Resolver/')
    ->ruleset([
        'Di'             => ['+Container', 'ServiceManager'],
        'CodeGenerator'  => ['+Resolver'],
        'Container'      => ['+CodeGenerator'],
        'ServiceManager' => ['Container'],
        'Definition'     => ['Reflection', 'Exception'],
        'Reflection'     => ['Definition', 'Exception'],
        'Exception'      => [],
        'Resolver'       => ['Di', '+Definition'],
    ]);
