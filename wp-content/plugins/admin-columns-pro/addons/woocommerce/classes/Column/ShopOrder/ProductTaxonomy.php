<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use WC_Order_Item_Product;
use WP_Term;

abstract class ProductTaxonomy extends AC\Column implements ACP\Export\Exportable, ACP\Search\Searchable,
                                                            ACP\ConditionalFormat\Formattable
{

    use FilteredHtmlFormatTrait;

    protected $taxonomy;

    public function __construct()
    {
        $this->set_group('woocommerce');
    }

    public function get_value($order_id)
    {
        $terms = ac_helper()->taxonomy->get_term_links($this->get_raw_value($order_id), 'product');

        if (empty($terms)) {
            return $this->get_empty_char();
        }

        return ac_helper()->string->enumeration_list($terms, 'and');
    }

    public function get_raw_value($order_id)
    {
        $result = [];

        foreach (wc_get_order($order_id)->get_items() as $item) {
            if ( ! $item instanceof WC_Order_Item_Product) {
                continue;
            }

            $terms = get_the_terms($item->get_product_id(), $this->get_taxonomy());

            if ( ! $terms || is_wp_error($terms)) {
                continue;
            }

            foreach ($terms as $term) {
                if ($term instanceof WP_Term) {
                    $result[$term->term_id] = $term;
                }
            }
        }

        return $result;
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\ShopOrder\ProductTaxonomy((string)$this->get_taxonomy());
    }

}