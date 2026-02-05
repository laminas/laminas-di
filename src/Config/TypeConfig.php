<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

use ArrayAccess;
use Laminas\Di\Config\Exception\InvalidConfigException;
use Laminas\Di\Config\Exception\InvalidParametersException;
use Laminas\Di\Config\Exception\InvalidTypePreferenceException;
use Laminas\Di\Config\Exception\UndefinedClassException;

use function assert;
use function class_exists;
use function get_debug_type;
use function interface_exists;
use function is_array;
use function is_iterable;
use function is_string;
use function sprintf;

/**
 * @psalm-type TypeConfigArray = array{
 *   typeOf?: class-string|null,
 *   preferences?: array<string, string>|null,
 *   parameters?: array<string, mixed>|null
 *  }
 */
final readonly class TypeConfig
{
    /**
     * @param string $name The name of the configured type.
     */
    public function __construct(
        public string $name,
        public TypePreferences $preferences = new TypePreferences(),
        public ParameterMap $parameters = new ParameterMap(),
    ) {
    }

    /**
     * Returns the class name for this type config
     *
     * @throws UndefinedClassException When the configured type does not exist.
     * @return class-string
     */
    public function getClassName(): string
    {
        if (! class_exists($this->name) && ! interface_exists($this->name)) {
            throw new UndefinedClassException($this->name);
        }

        return $this->name;
    }

    /**
     * @return array<string, TypeConfig|AliasConfig> $config
     */
    public static function mapFromConfigValue(mixed $config): array
    {
        if (! is_iterable($config)) {
            throw new InvalidConfigException('The di type configuration must be iterable');
        }

        $map = [];

        /** @var mixed $value */
        foreach ($config as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidConfigException(
                    sprintf(
                        'A type configuration key must be a string, got %s',
                        get_debug_type($key),
                    ),
                );
            }

            if (! is_array($value) && ! $value instanceof ArrayAccess) {
                throw new InvalidConfigException(
                    sprintf(
                        'Type configuration for "%s" is expected to be an array or array accessible object, got %s',
                        $key,
                        get_debug_type($value)
                    ),
                );
            }

            /** @var string|null $alias */
            $alias     = null;
            $className = $key;

            try {
                $preferences = TypePreferences::fromConfigValue($value['preferences'] ?? []);
                $parameters  = ParameterMap::fromConfigValue($value['parameters'] ?? []);
            } catch (InvalidTypePreferenceException $preferencesException) {
                throw new InvalidConfigException(
                    sprintf(
                        'Invalid type preferences for "%s": %s',
                        $key,
                        $preferencesException->getMessage(),
                    ),
                    $preferencesException->getCode(),
                    $preferencesException,
                );
            } catch (InvalidParametersException $paramsException) {
                throw new InvalidConfigException(
                    sprintf(
                        'Invalid parameter config for "%s": %s',
                        $key,
                        $paramsException->getMessage(),
                    ),
                    $paramsException->getCode(),
                    $paramsException,
                );
            }

            if (isset($value['typeOf'])) {
                assert(is_string($value['typeOf']));
                $alias     = $className;
                $className = $value['typeOf'];
            }

            $type = new self($className, $preferences, $parameters);

            if ($alias !== null) {
                $type = new AliasConfig($alias, $type);
            }

            $map[$type->name] = $type;
        }

        return $map;
    }
}
