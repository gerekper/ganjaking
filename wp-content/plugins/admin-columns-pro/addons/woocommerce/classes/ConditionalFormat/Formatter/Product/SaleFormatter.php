<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat\Formatter\Product;

use AC\Column;
use ACP\ConditionalFormat\Formatter;
use ACP\Expression\ComparisonOperators;

class SaleFormatter implements Formatter
{

    public function get_type(): string
    {
        return self::FLOAT;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        if (ComparisonOperators::class === $operator_group) {
            return (string)get_post_meta($id, '_sale_price', true);
        }

        return $value;
    }

}