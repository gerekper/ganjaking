<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACP;

/**
 * @since 1.1
 */
class ShippingClass extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-shipping_class')
             ->set_label(__('Shipping Class', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_taxonomy()
    {
        return 'product_shipping_class';
    }

    public function get_value($post_id)
    {
        $term = get_term_by('id', $this->get_raw_value($post_id), $this->get_taxonomy());

        if ( ! $term) {
            return $this->get_empty_char();
        }

        return ac_helper()->taxonomy->get_term_display_name($term);
    }

    public function get_raw_value($post_id)
    {
        return wc_get_product($post_id)->get_shipping_class_id();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Taxonomy($this->get_taxonomy());
    }

    public function editing()
    {
        return new Editing\Product\ShippingClass();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\Taxonomy($this->get_taxonomy());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}