<?php

namespace ACP\Sorting\Type;

use LogicException;

class DataType
{

    public const STRING = 'string';
    public const NUMERIC = 'numeric';
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const DECIMAL = 'decimal';

    private $value;

    public function __construct(string $value)
    {
        if ( ! self::is_valid($value)) {
            throw new LogicException('Invalid data type.');
        }

        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }

    public static function is_valid(string $value): bool
    {
        return in_array($value, [self::STRING, self::NUMERIC, self::DATE, self::DATETIME, self::DECIMAL], true);
    }

    public function __toString()
    {
        return $this->value;
    }

}