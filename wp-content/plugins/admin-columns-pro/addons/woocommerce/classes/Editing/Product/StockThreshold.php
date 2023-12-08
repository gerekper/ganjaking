<?php

namespace ACA\WC\Editing\Product;

use ACP;
use ACP\Editing\View;

class StockThreshold implements ACP\Editing\Service
{

    public function get_view(string $context): ?View
    {
        return new ACP\Editing\View\Number();
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);

        return $product
            ? $product->get_low_stock_amount()
            : false;
    }

    public function update(int $id, $data): void
    {
        $product = wc_get_product($id);
        $product->set_low_stock_amount($data);
        $product->save();
    }

}