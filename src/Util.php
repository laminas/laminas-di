<?php

declare(strict_types=1);

namespace Laminas\Di;

/**
 * @internal
 */
final class Util
{
    private function __construct()
    {
    }

    /**
     * Map an iterable to an associative array
     *
     * @template ItemType
     * @template KeyType of array-key
     * @template MappedType
     * @param iterable<mixed, ItemType>              $array
     * @param callable(ItemType, KeyType):MappedType $mapValue
     * @param (callable(mixed, ItemType):KeyType)|null $mapKey
     * @return array<KeyType, MappedType>
     */
    public static function mapIterable(iterable $array, callable $mapValue, callable|null $mapKey = null): array
    {
        $mapped = [];

        /** @psalm-var KeyType $key Assume key type when mapper accepts it without throwing a TypeError */
        foreach ($array as $key => $value) {
            if ($mapKey !== null) {
                $key = $mapKey($key, $value);
            }

            $mapped[$key] = $mapValue($value, $key);
        }

        return $mapped;
    }
}
