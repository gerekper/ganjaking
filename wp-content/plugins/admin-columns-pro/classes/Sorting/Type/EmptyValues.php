<?php

namespace ACP\Sorting\Type;

use LogicException;

class EmptyValues
{

    public const NULL = null;
    public const EMPTY_STRING = '';
    public const ZERO = 0;
    public const LTE_ZERO = -1;

    private $values;

    public function __construct(array $values)
    {
        if ( ! self::is_valid($values)) {
            throw new LogicException('Invalid values.');
        }

        $this->values = $values;
    }

    public static function is_valid(array $values): bool
    {
        return $values && count($values) === count(array_filter($values, [__CLASS__, 'is_valid_value']));
    }

    public static function is_valid_value($value): bool
    {
        return in_array($value, [self::NULL, self::EMPTY_STRING, self::ZERO, self::LTE_ZERO], true);
    }

    public function has_value($value): bool
    {
        return in_array($value, $this->values, true);
    }

    public function get_values(): array
    {
        return $this->values;
    }

}