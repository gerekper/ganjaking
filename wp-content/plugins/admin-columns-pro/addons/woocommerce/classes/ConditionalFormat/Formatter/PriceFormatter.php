<?php

namespace ACA\WC\ConditionalFormat\Formatter;

use AC\Column;
use ACP\ConditionalFormat\Formatter;
use ACP\Expression\ComparisonOperators;

class PriceFormatter extends Formatter\FloatFormatter
{

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        if (ComparisonOperators::class === $operator_group) {
            $formatted_value = $column->get_raw_value($id);
            $value = is_string($formatted_value) || is_float($formatted_value) ? (string)$formatted_value : '';
        }

        return trim(strip_tags(html_entity_decode($value)));
    }

}