# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
