# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.2.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.2.0 - 2020-10-21

### Added

- [zendframework/zend-di#51](https://github.com/zendframework/zend-di/pull/51) adds `Laminas\Di\GeneratedInjectorDelegator` to decorate the
  default injector with an AoT generated one.
- [#11](https://github.com/laminas/laminas-di/pull/11) adds a config option to configure a logger for the AoT generator.

### Removed

- [zendframework/zend-di#48](https://github.com/zendframework/zend-di/pull/48) removes support for laminas-stdlib v2 releases.


-----

### Release Notes for [3.2.0](https://github.com/laminas/laminas-di/milestone/1)



### 3.2.0

- Total issues resolved: **1**
- Total pull requests resolved: **6**
- Total contributors: **5**

#### Documentation

 - [19: Add support for multiple releases in documentation](https://github.com/laminas/laminas-di/pull/19) thanks to @tux-rampage and @froschdesign

#### Enhancement,hacktoberfest-accepted

 - [16: Migrate to laminas/laminas-coding-standard v2](https://github.com/laminas/laminas-di/pull/16) thanks to @gennadigennadigennadi
 - [15: Bump supported php versions](https://github.com/laminas/laminas-di/pull/15) thanks to @gennadigennadigennadi

#### Review Needed

 - [14: Merge release 3.1.3 into 3.2.x](https://github.com/laminas/laminas-di/pull/14) thanks to @github-actions[bot]

#### Bug

 - [12: Fix handling of aot namespace config](https://github.com/laminas/laminas-di/pull/12) thanks to @tux-rampage

#### Enhancement

 - [11: Custom logger](https://github.com/laminas/laminas-di/pull/11) thanks to @waahhhh

## 3.1.3 - 2020-09-16


-----

### Release Notes for [3.1.3](https://github.com/laminas/laminas-di/milestone/2)



### 3.1.3

- Total issues resolved: **0**
- Total pull requests resolved: **1**
- Total contributors: **1**

#### Documentation,Enhancement

 - [13: Adds new page for installation to documentation](https://github.com/laminas/laminas-di/pull/13) thanks to @froschdesign

## 3.1.2 - 2019-12-10

### Added

- [zendframework/zend-di#56](https://github.com/zendframework/zend-di/pull/56) adds support for PHP 7.3 and 7.4.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-di#56](https://github.com/zendframework/zend-di/pull/56) fixes PHP 7.4 compatibility.

## 3.1.1 - 2019-01-15

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-di#49](https://github.com/zendframework/zend-di/pull/49) removes checking type of
  class/interface typehinted parameter.
- [zendframework/zend-di#50](https://github.com/zendframework/zend-di/pull/50) fixes recognizing array values
  as exportable, so factories can be generated for default array values.

## 3.1.0 - 2018-10-23

### Added

- [zendframework/zend-di#34](https://github.com/zendframework/zend-di/pull/34) adds the ability to pass a
  `Psr\Log\LoggerInterface` instance to the constructor of `Laminas\Di\CodeGenerator\InjectorGenerator` 
  (e.g. `new InjectorGenerator($config, $resolver, $namespace, $logger)`)

- [zendframework/zend-di#31](https://github.com/zendframework/zend-di/pull/31) adds the service
  factory `Laminas\Di\Container\GeneratorFactory` for creating a
  `Laminas\Di\CodeGenerator\InjectorGenerator` instance with laminas-servicemanager.

- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) adds `Laminas\Di\Resolver\InjectionInterface` that defines the
  return type of `Laminas\Di\Resolver\DependencyResolverInterface::resolveParameters()` to prepare a stable interface for 
  future releases. This will not affect you unless you have implemented a custom dependency resolver that returns other 
  items than `Laminas\Di\Resolver\TypeInjection` or `Laminas\Di\Resolver\ValueInjection`. In this case you need to change the 
  returned items to implement this interface.

- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) adds parameter and return types to:
  - `Laminas\Di\CodeGenerator\AutoloadGenerator`
  - `Laminas\Di\CodeGenerator\FactoryGenerator`
  - `Laminas\Di\CodeGenerator\InjectorGenerator` 

### Changed

- [zendframework/zend-di#31](https://github.com/zendframework/zend-di/pull/31) adds the method
  `getOutputDirectory()` to `Laminas\Di\CodeGenerator\GeneratorTrait`.

- [zendframework/zend-di#31](https://github.com/zendframework/zend-di/pull/31) adds the method
  `getNamespace()` to `Laminas\Di\CodeGenerator\InjectorGenerator`.

- [zendframework/zend-di#37](https://github.com/zendframework/zend-di/pull/37) removes the use of `count()` 
  in `Laminas\Di\CodeGenerator\FactoryGenerator::buildParametersCode()` to improve performance   

- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) adds strictness to
  `Laminas\Di\CodeGenerator\FactoryGenerator::generate()`:
  - Adds `string` return type.
  - Adds throw of `RuntimeException` on failures.  
  
- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) removes inheritance of 
  `Laminas\Di\Resolver\AbstractInjection`:
   - from `Laminas\Di\Resolver\ValueInjection`
   - from `Laminas\Di\Resolver\TypeInjection`
   
- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) adds implementation of 
  `Laminas\Di\Resolver\InjectionInterface`:
   - to `Laminas\Di\Resolver\ValueInjection`
   - to `Laminas\Di\Resolver\TypeInjection`
  
### Deprecated

- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) deprecates `Laminas\Di\Resolver\AbstractInjection`.
  in favour of `Laminas\Di\Resolver\InjectionInterface`
  
- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) deprecates `Laminas\Di\Resolver\TypeInjection::getType`
  in favour of `__toString()`.
  
- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) deprecates `Laminas\Di\Resolver\ValueInjection::getValue()`
  in favour of `toValue()`.

### Removed

- [zendframework/zend-di#38](https://github.com/zendframework/zend-di/pull/38) removes usage of `laminas-code`

### Fixed

- [zendframework/zend-di#36](https://github.com/zendframework/zend-di/pull/36) fixes incorrect 
  phpdocs in `Laminas\Di\Injector`.

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
