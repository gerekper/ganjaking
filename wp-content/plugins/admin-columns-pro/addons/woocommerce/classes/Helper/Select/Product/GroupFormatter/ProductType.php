<?php

declare(strict_types=1);

namespace ACA\WC\Helper\Select\Product\GroupFormatter;

use ACA\WC\Helper\Select\Product\GroupFormatter;
use WC_Product;

class ProductType implements GroupFormatter
{

    public function format(WC_Product $product): string
    {
        $types = wc_get_product_types();

        return $types[$product->get_type()] ?? $product->get_type();
    }

}