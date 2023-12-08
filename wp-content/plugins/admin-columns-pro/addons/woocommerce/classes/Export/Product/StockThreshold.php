<?php

namespace ACA\WC\Export\Product;

use ACP;

class StockThreshold implements ACP\Export\Service
{

    public function get_value($id)
    {
        return (string)wc_get_product($id)->get_low_stock_amount();
    }

}