# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.0.0 - 2017-11-30

### Added

- `Laminas\Di\DefaultContainer` implementing `Psr\Container\ContainerInterface`:
  - Can act as a standalone IoC container.
  - Provides `build()` to be signature compatible with `Laminas\ServiceManager\ServiceManager`.

- `Laminas\Di\Injector` implementing `Laminas\Di\InjectorInterface`
  - Designed to compose a `Psr\Container\ContainerInterface` implementation for
    purposes of resolving dependencies. By default, this is the `DefaultContainer`
    implementation.
  - Utilizes `Laminas\Di\Resolver\DependencyResolverInterface` to resolve arguments
    to their types.

- PHP 7.1 type safety.

- Classes to wrap value and type injections.

- Support for laminas-component-installer. This allows it to act as a standalone
  config-provider or laminas-mvc module, and eliminates the need for
  laminas-servicemanager-di.

- `Laminas\Di\ConfigInterface` to allow providing custom configuration.

- Code generator for generating a pre-resolved injector and factories.

### Changed

- Renames `Laminas\Di\DependencyInjectionInterface` to `Laminas\Di\InjectorInterface`.
  It defines the injector to create new instances based on a class or alias
  name.
  - `newInstance()` changes to `create()`.
  - `has()` changes to `canCreate()`.
  - Removes `get()`.

- Moves strategies to resolve method parameters to `Laminas\Di\Resolver`

### Deprecated

- Nothing

### Removed

- Support for PHP versions less than 7.1

- Support for HHVM.

- `Laminas\Di\Defintion\CompilerDefinition` in favour of `Laminas\Di\CodeGenerator`.

- `Laminas\Di\InstanceManager`, `Laminas\Di\ServiceLocator`, `Laminas\Di\ServiceLocatorInterface`
  and `Laminas\Di\LocatorInterface` in favor of `Psr\Container\ContainerInterface`.

- `Laminas\Di\Di` is removed in favour of `Laminas\Di\DefaultContainer`.

- `Laminas\Di\DefinitionList`

- `Laminas\Di\Definition\BuilderDefinition`

- `Laminas\Di\Definition\ArrayDefinition`

- Parameters passed to `newInstance()` will only be used for constructing the
  requested class and no longer be forwarded to nested objects.

- `get()` no longer supports a `$parameters` array; `newInstance()` still does.

- Removed setter/method injections.

- Generators in `Laminas\Di\ServiceLocator` in favor of `Laminas\Di\CodeGenerator`.

### Fixed

- [zendframework/zend-di#6](https://github.com/zendframework/zend-di/pull/6) Full Laminas Compatibility.
- [zendframework/zend-di#18](https://github.com/zendframework/zend-di/issues/18) DI Runtime Compiler
  Definition.

## 2.6.1 - 2016-04-25

### Added

- Adds all existing documentation and publishes it at
  https://docs.laminas.dev/laminas-di/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-di#3](https://github.com/zendframework/zend-di/pull/3) fixes how
  `InstanceManager::sharedInstancesWithParams()` behaves when multiple calls are
  made with different sets of parameters (it should return different instances
  in that situation).

## 2.6.0 - 2016-02-23

### Added

- [zendframework/zend-di#16](https://github.com/zendframework/zend-di/pull/16) adds container-interop
  as a dependency, and updates the `LocatorInterface` to extend
  `Interop\Container\ContainerInterface`. This required adding the following
  methods:
  - `Laminas\Di\Di::has()`
  - `Laminas\Di\ServiceLocator::has()`

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-di#15](https://github.com/zendframework/zend-di/pull/15) and
  [zendframework/zend-di#16](https://github.com/zendframework/zend-di/pull/16) remove most
  development dependencies, as the functionality could be reproduced with
  generic test assets or PHP built-in classes. These include:
  - laminas-config
  - laminas-db
  - laminas-filter
  - laminas-log
  - laminas-mvc
  - laminas-view
  - laminas-servicemanager

### Fixed

- [zendframework/zend-di#16](https://github.com/zendframework/zend-di/pull/16) updates the try/catch
  block in `Laminas\Di\Di::resolveMethodParameters()` to catch container-interop
  exceptions instead of the laminas-servicemanager-specific exception class. Since
  all laminas-servicemanager exceptions derive from container-interop, this
  provides more flexibility in using any container-interop implementation as a
  peering container.
