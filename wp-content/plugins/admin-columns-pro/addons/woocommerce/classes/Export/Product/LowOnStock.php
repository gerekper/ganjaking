<?php

namespace ACA\WC\Export\Product;

use ACP;

class LowOnStock implements ACP\Export\Service
{

    public function get_value($id)
    {
        $product = wc_get_product($id);

        $threshold_product = (int)$product->get_low_stock_amount();
        $threshold_global = (int)get_option('woocommerce_notify_low_stock_amount', 0);

        if ($threshold_product <= 0 && $threshold_global <= 0) {
            return '';
        }

        $stock = (int)$product->get_stock_quantity();

        $threshold = $threshold_product ?: $threshold_global;

        return $stock <= $threshold ? '1' : '0';
    }

}