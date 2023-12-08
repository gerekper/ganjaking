<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat\Formatter\Product;

use AC\Column;
use ACP\ConditionalFormat\Formatter;
use ACP\Expression\ComparisonOperators;

class AvgOrderIntervalFormatter implements Formatter
{

    public function get_type(): string
    {
        return self::INTEGER;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        if (ComparisonOperators::class === $operator_group) {
            return (string)$column->get_raw_value($id); // returns number of days
        }

        return $value;
    }

}