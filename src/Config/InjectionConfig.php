<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

use ArrayAccess;
use Laminas\Di\Config\Exception\InvalidConfigException;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Exception\LogicException;

use function array_keys;
use function get_debug_type;
use function is_array;
use function sprintf;

/**
 * Provides a typesafe and immutable DI configuration from a config array.
 *
 *  This configures the instantiation process of the dependency injector.
 *
 *  **Example:**
 *
 *  <code>
 *  return [
 *      // This section provides global type preferences.
 *      // Those are visited if a specific instance has no preference definitions.
 *      'preferences' => [
 *          // The key is the requested class or interface name, the values are
 *          // the types the dependency injector should prefer.
 *          Some\Interface::class => Some\Preference::class
 *      ],
 *      // This configures the instantiation of specific types.
 *      // Types may also be purely virtual by defining the aliasOf key.
 *      'types' => [
 *          My\Class::class => [
 *               'preferences' => [
 *                   // this supercedes the global type preferences
 *                   // when My\Class is instantiated
 *                   Some\Interface::class => 'My.SpecificAlias'
 *               ],
 *
 *               // instantiation parameters. These will only be used for
 *               // the instantiator (i.e. the constructor)
 *               'parameters' => [
 *                   'foo' => My\FooImpl::class, // Use the given type to provide the injection (depends on definition)
 *                   'bar' => '*' // Use the type preferences
 *               ],
 *          ],
 *
 *          'My.Alias' => [
 *              // typeOf defines virtual classes which can be used as type
 *              // preferences or for newInstance calls. They allow providing
 *              // custom configs for a class
 *              'typeOf' => Some\Class::class,
 *              'preferences' => [
 *                   Foo::class => Bar::class
 *              ]
 *          ]
 *      ]
 *  ];
 *  </code>
 *
 *  ## Notes on Injections
 *
 *  Named arguments and Automatic type lookups will only work for Methods that
 *  are known to the dependency injector through its definitions. Injections for
 *  unknown methods do not perform type lookups on its own.
 *
 *  A value injection without any lookups can be forced by providing a
 *  Resolver\ValueInjection instance.
 *
 *  To force a service/class instance provide a Resolver\TypeInjection instance.
 *  For classes known from the definitions, a type preference might be the
 *  better approach
 *
 * @see \Laminas\Di\Resolver\ValueInjection A container to force injection of a value
 * @see \Laminas\Di\Resolver\TypeInjection  A container to force looking up a specific type instance for injection
 */
final readonly class InjectionConfig implements ConfigInterface
{
    /**
     * @param array<string, TypeConfig|AliasConfig> $types
     */
    public function __construct(
        private TypePreferences $preferences = new TypePreferences(),
        private array $types = []
    ) {
    }

    /**
     * Constructs the injection config from a laminas config value
     */
    public static function fromConfigValue(mixed $config): self
    {
        if (! is_array($config) && ! $config instanceof ArrayAccess) {
            throw new InvalidConfigException(
                sprintf(
                    'Di configuration must be an array or implement ArrayAccess, got %s',
                    get_debug_type($config),
                ),
            );
        }

        return new self(
            TypePreferences::fromConfigValue($config['preferences'] ?? []),
            TypeConfig::mapFromConfigValue($config['types'] ?? []),
        );
    }

    private function getTypeConfig(string $name): TypeConfig|null
    {
        $type = $this->types[$name] ?? null;
        return $type instanceof AliasConfig ? $type->type : $type;
    }

    public function isAlias(string $name): bool
    {
        $type = $this->types[$name] ?? null;
        return $type instanceof AliasConfig;
    }

    public function getConfiguredTypeNames(): array
    {
        return array_keys($this->types);
    }

    public function getClassForAlias(string $name): string|null
    {
        return $this->getTypeConfig($name)?->getClassName();
    }

    public function getParameters(string $type): array
    {
        /**
         * Psalm-Bug: https://github.com/vimeo/psalm/issues/7099
         *
         * @psalm-suppress RedundantCondition
         * @psalm-suppress TypeDoesNotContainNull
         */
        return $this->getTypeConfig($type)?->parameters->toArray() ?? [];
    }

    public function setParameters(string $type, array $params)
    {
        throw new LogicException(
            'Injection config is considered immutable. You can set [dependencies][auto][mutableConfig] to '
            . 'true to restore the previous, but deprecated, behavior'
        );
    }

    public function getTypePreference(string $type, string|null $contextClass = null): string|null
    {
        if ($contextClass !== null) {
            $preference = $this->getTypeConfig($contextClass)?->preferences->getPreferenceFor($type);

            if ($preference !== null) {
                return $preference;
            }
        }

        return $this->preferences->getPreferenceFor($type);
    }
}
