<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC;
use ACP;
use WC_Order_Item_Product;
use WP_Term;

class ProductTaxonomy extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                                   ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_product_taxonomy')
             ->set_label(__('Product Taxonomy', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $terms = [];

        foreach ($this->get_product_ids($order) as $product_id) {
            foreach ($this->get_terms($product_id) as $term) {
                if ($term instanceof WP_Term) {
                    $terms[$term->term_id] = $term;
                }
            }
        }

        $terms = ac_helper()->taxonomy->get_term_links($terms, 'product');

        return ! empty($terms)
            ? ac_helper()->string->enumeration_list($terms, 'and')
            : $this->get_empty_char();
    }

    private function get_product_ids(\WC_Order $order): array
    {
        $ids = [];

        foreach ($order->get_items() as $order_item) {
            if ( ! $order_item instanceof WC_Order_Item_Product) {
                continue;
            }

            $ids[] = $order_item->get_product_id();
        }

        return $ids;
    }

    public function get_terms($post_id)
    {
        $terms = get_the_terms($post_id, $this->get_taxonomy());

        if ( ! $terms || is_wp_error($terms)) {
            return [];
        }

        return $terms;
    }

    public function get_taxonomy()
    {
        $setting = $this->get_setting('taxonomy');

        return $setting instanceof AC\Settings\Column\Taxonomy
            ? $setting->get_taxonomy() ?? false
            : false;
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new AC\Settings\Column\Taxonomy($this, 'product'));
    }

    public function search()
    {
        $taxonomy = $this->get_taxonomy();

        if (false === $taxonomy) {
            return null;
        }

        return new WC\Search\Order\ProductTaxonomy($taxonomy);
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}