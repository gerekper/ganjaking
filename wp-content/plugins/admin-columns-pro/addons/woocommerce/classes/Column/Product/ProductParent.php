<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Settings\Product;
use ACP;

/**
 * @since 3.0
 */
class ProductParent extends AC\Column
    implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_group('woocommerce');
        $this->set_type('column-wc-product-parent');
        $this->set_label(__('Grouped By', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        $parents = $this->get_raw_value($id);

        if (empty($parents)) {
            return $this->get_empty_char();
        }

        /**
         * @var AC\Collection $values
         */
        $values = $this->get_formatted_value(new AC\Collection($parents));

        return $values->implode($this->get_separator());
    }

    public function get_raw_value($id)
    {
        return get_posts([
            'fields'         => 'ids',
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => '_children',
                    'value'   => serialize((int)$id),
                    'compare' => 'LIKE',
                ],
            ],
            'tax_query'      => [
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'grouped',
                ],
            ],
        ]);
    }

    public function register_settings()
    {
        $this->add_setting(new Product($this));
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}