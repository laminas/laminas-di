<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

use Laminas\Di\Config\Exception\InvalidParametersException;
use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Util;

use function array_map;
use function array_values;
use function get_debug_type;
use function is_iterable;
use function is_string;
use function sprintf;

final readonly class ParameterMap
{
    /** @var array<string, Parameter> */
    private array $parameters;

    public function __construct(Parameter ...$parameters)
    {
        $map = [];

        foreach ($parameters as $parameter) {
            $map[$parameter->name] = $parameter;
        }

        $this->parameters = $map;
    }

    /**
     * @param mixed $parameters the parameters array from the laminas config
     * @throws InvalidParametersException When a numeric key is encountered.
     */
    public static function fromConfigValue(mixed $parameters): self
    {
        if (! is_iterable($parameters)) {
            throw new InvalidParametersException(
                sprintf(
                    'Injection parameters must be an array, got %s',
                    get_debug_type($parameters)
                ),
            );
        }

        return new self(
            ...array_values(
                Util::mapIterable(
                    $parameters,
                    static function (mixed $value, string|int $key): Parameter {
                        if ($value instanceof Parameter) {
                            return $value;
                        }

                        if (! is_string($key)) {
                            throw InvalidParametersException::numericParamKey($key);
                        }

                        return Parameter::fromValue($key, $value);
                    },
                ),
            ),
        );
    }

    /**
     * @return array<string, string|InjectionInterface>
     */
    public function toArray(): array
    {
        return array_map(
            static fn (Parameter $parameter) => $parameter->injection,
            $this->parameters
        );
    }

    public function get(string $key): Parameter | null
    {
        return $this->parameters[$key] ?? null;
    }
}
