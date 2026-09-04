<?php

declare(strict_types=1);

namespace Laminas\Di\Config;

use Laminas\Di\Config\Exception\InvalidTypePreferenceException;
use Laminas\Di\Util;
use Stringable;

use function get_debug_type;
use function is_iterable;
use function is_string;
use function sprintf;

final readonly class TypePreferences
{
    /**
     * @param array<array-key, string> $preferences
     */
    public function __construct(
        public array $preferences = [],
    ) {
    }

    /**
     * Converts the laminas config array to a type save variant
     *
     * @param mixed $preferences The value from the laminas config array
     * @throws InvalidTypePreferenceException When the config array contains unexpected values.
     */
    public static function fromConfigValue(mixed $preferences): self
    {
        if (! is_iterable($preferences)) {
            throw new InvalidTypePreferenceException(
                sprintf(
                    'Type preferences mus be an iterable item, got "%s".',
                    get_debug_type($preferences),
                ),
            );
        }

        return new self(Util::mapIterable(
            $preferences,
            static fn (mixed $item, string|int $key): string => is_string($item) || $item instanceof Stringable
                ? "$item"
                : throw new InvalidTypePreferenceException(
                    sprintf(
                        'A type preference for %s must be a string, got %s.',
                        (string) $key,
                        get_debug_type($item),
                    ),
                ),
        ));
    }

    /**
     * Returns the configured preference of a requested type
     *
     * @param string $type The requested type to resolve
     */
    public function getPreferenceFor(string $type): string | null
    {
        return $this->preferences[$type] ?? null;
    }
}
