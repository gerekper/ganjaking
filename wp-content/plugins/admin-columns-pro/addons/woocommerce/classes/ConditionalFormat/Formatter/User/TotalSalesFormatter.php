<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat\Formatter\User;

use AC\Column;
use ACP\ConditionalFormat\Formatter;

class TotalSalesFormatter extends Formatter\FloatFormatter
{

    public function get_type(): string
    {
        return self::FLOAT;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        $totals = $column->get_raw_value($id);

        if (is_array($totals)) {
            return (string)reset($totals);
        }

        return is_scalar($totals)
            ? (string)$totals
            : '';
    }

}