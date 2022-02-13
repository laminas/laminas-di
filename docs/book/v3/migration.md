# Migration Guide

Version 3 is the first new major release of laminas-di, and contains a number of
backward incompatible changes. These were introduced to provide better
performance, stability, and predictability.

This guide describes how to migrate from Version 2 to 3.

## What has changed?

This lists the most impacting changes and potential pitfalls when
upgrading to `laminas-di` version 3.

- The injector now only supports constructor injections. If you require
  injections based on "Aware" interfaces or setter/method injections, you need
  to provide these on your own. You could do this by decorating the injector
  instance or using initializers in laminas-servicemanager.

- `\Laminas\Di\Di` is renamed to `\Laminas\Di\InjectorInterface`. It also is no longer
  an IoC container which offers `get()`/`has()`. Container functionality is now
  provided via `Laminas\Di\DefaultContainer`, which implements
  `Psr\Container\ContainerInterface`.  If you were using `\Laminas\Di\Di` as an IoC
  container, please switch to `Laminas\Di\DefaultContainer` or use it with
  [laminas-servicemanager](cookbook/use-with-servicemanager.md).

- All programmatic and array-based definitions were dropped. If you need custom
  definitions, implement `\Laminas\Di\Definition\DefinitionInterface`.

- The definition compiler was removed in favor of a [code generator](codegen.md),
  which offers better performance.

- Added PHP 7.1 type safety. All interfaces and classes are strongly typed, and
  all methods use return typehints, as well as scalar typehints where needed.

- `Generator` and `GeneratorInstance` in `Laminas\Di\ServiceLocator` were removed
  in favor of the [code generator](codegen.md), which creates laminas-servicemanager
  compatible factories.

## Migrating from v2 to v3 with laminas-mvc

When you are using laminas-mvc, you can follow these steps to upgrade:

1. Remove `laminas/laminas-servicemanager-di` from your `composer.json`, or
   execute `composer remove laminas/laminas-servicemanager-di`.
2. Change the version constraint for `laminas/laminas-di` to `^3.0`, or
   execute `composer require "laminas/laminas-di:^3.0"`.
3. Change any remaining module entries for `Laminas\ServiceManager\Di\Module` to
   `Laminas\Di\Module` within either `config/application.config.php` or
   `config/modules.config.php`.
4. If you are using any factories from laminas-servicemanager-di, you may have to
   replace them with `Laminas\Di\Container\AutowireFactory`.
5. Migrate your laminas-di config to the new [configuration format](config.md).

## Migrating configuration

laminas-di configuration is now expected in `$config['dependencies']['auto']`,
where `$config` is your `config` service.

The laminas-di config service factory will automatically attempt to migrate legacy
configurations at runtime, which gives you some time to migrate your configs.
You can use `Laminas\Di\LegacyConfig` to help migrating existing configs:

```php
use Laminas\Di\LegacyConfig;

$migrated = new LegacyConfig($diConfigArray);
$code = var_export($migrated->toArray(), true);
```

When the laminas-di config service factory is used to create configuration for use
with the `AutowireFactory` and/or `DefaultContainer`, if it detects the
`$config['di']` key, it will emit an `E_USER_DEPRECATED` error, pointing to this
documentation. We recommend using the above approach to convert your
configuration to the format recognized by version 3.
