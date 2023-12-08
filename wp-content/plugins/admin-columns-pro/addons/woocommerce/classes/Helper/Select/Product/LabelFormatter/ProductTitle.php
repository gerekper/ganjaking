<?php

declare(strict_types=1);

namespace ACA\WC\Helper\Select\Product\LabelFormatter;

use ACA\WC\Helper\Select\Product\LabelFormatter;
use WC_Product;
use WC_Product_Variation;

class ProductTitle implements LabelFormatter
{

    public function format_label_unique(WC_Product $product): string
    {
        return sprintf('%s (%s)', $this->format_label($product), $product->get_id());
    }

    public function format_label(WC_Product $product): string
    {
        $label = '#' . $product->get_id();
        $title = $product->get_title();
        $sku = $product->get_sku();

        if ($title) {
            $label .= ' ' . $title;
        }

        if ($sku) {
            $label .= sprintf(' (%s)', $sku);
        }

        if ($product instanceof WC_Product_Variation) {
            $attributes = array_values($product->get_attributes());

            $label .= sprintf(' (%s)', implode(', ', $attributes));
        }

        return $label;
    }

}