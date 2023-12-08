<?php

namespace ACA\WC\Helper\Select;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Helper\Select\Product\LabelFormatter\ProductTitle;
use ACA\WC\Helper\Select\Product\PaginatedFactory;

trait ProductAndVariationValuesTrait
{

    public function format_label($value): string
    {
        $product = wc_get_product($value);

        return $product
            ? $this->get_label_formatter()->format_label($product)
            : '';
    }

    protected function get_label_formatter(): ProductTitle
    {
        return new ProductTitle();
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => ['product', 'product_variation'],
        ], $this->get_label_formatter());
    }

}