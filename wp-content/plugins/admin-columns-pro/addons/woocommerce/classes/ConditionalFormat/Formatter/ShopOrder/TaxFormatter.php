<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat\Formatter\ShopOrder;

use AC\Column;
use ACP\ConditionalFormat\Formatter;

class TaxFormatter implements Formatter
{

    public function get_type(): string
    {
        return self::STRING;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        $taxes = [];

        foreach (wc_get_order($id)->get_tax_totals() as $tax_total) {
            $taxes[] = $tax_total->amount;
        }

        return implode(' ', $taxes);
    }

}