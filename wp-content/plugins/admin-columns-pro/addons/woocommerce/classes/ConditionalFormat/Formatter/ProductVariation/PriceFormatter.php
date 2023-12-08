<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat\Formatter\ProductVariation;

use AC\Column;
use ACP\ConditionalFormat\Formatter;
use ACP\Expression\ComparisonOperators;
use WC_Product_Variation;

class PriceFormatter extends Formatter\FloatFormatter
{

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        if (ComparisonOperators::class === $operator_group) {
            $value = (new WC_Product_Variation($id))->get_price();
        }

        return trim(strip_tags(html_entity_decode($value)));
    }

}