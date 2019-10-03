<?php

namespace Selective\CdChecker;

use InvalidArgumentException;

/**
 * Class.
 */
final class TypeCast
{
    /**
     * Converts the representation of a number to its integer equivalent.
     *
     * @param mixed $value The representation of a number
     *
     * @return int The integer equivalent
     */
    public static function castInt($value): int
    {
        if ($value !== null && !is_scalar($value)) {
            throw new InvalidArgumentException('Value could not be parsed to integer');
        }

        return (int)$value;
    }

    /**
     * Converts the representation of a string to its string equivalent.
     *
     * @param mixed $value The representation of a string
     *
     * @return string The array equivalent
     */
    public static function castString($value): string
    {
        if ($value !== null && !is_scalar($value)) {
            throw new InvalidArgumentException('Value could not be parsed to string');
        }

        return (string)$value;
    }
}
