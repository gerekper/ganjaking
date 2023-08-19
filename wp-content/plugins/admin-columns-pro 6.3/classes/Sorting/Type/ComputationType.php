<?php

declare(strict_types=1);

namespace ACP\Sorting\Type;

use LogicException;

/**
 * SQL cast type e.g. AVG, MIN, MAX
 */
class ComputationType
{

    public const AVG = 'AVG';
    public const COUNT = 'COUNT';
    public const MIN = 'MIN';
    public const MAX = 'MAX';
    public const ROUND = 'ROUND';
    public const SUM = 'SUM';

    private $value;

    public function __construct(string $value)
    {
        if ( ! self::is_valid($value)) {
            throw new LogicException('Invalid Computation type.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->get_value();
    }

    public function get_value(): string
    {
        return $this->value;
    }

    public static function is_valid(string $value): bool
    {
        return in_array($value, [self::AVG, self::COUNT, self::MIN, self::MAX, self::ROUND, self::SUM], true);
    }

}