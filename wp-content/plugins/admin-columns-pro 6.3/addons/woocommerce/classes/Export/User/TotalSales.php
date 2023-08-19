<?php

namespace ACA\WC\Export\User;

use ACA\WC\Column;
use ACP;

class TotalSales implements ACP\Export\Service
{

    protected $column;

    public function __construct(Column\User\ShopOrder\TotalSales $column)
    {
        $this->column = $column;
    }

    public function get_value($id)
    {
        $totals = $this->column->get_raw_value($id);

        if ( ! $totals) {
            return '';
        }

        $values = [];

        foreach ($totals as $currency => $amount) {
            $values[] = get_woocommerce_currency_symbol($currency) . $amount;
        }

        return implode(', ', $values);
    }

}