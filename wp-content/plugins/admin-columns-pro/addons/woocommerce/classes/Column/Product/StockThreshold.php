<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;

class StockThreshold extends AC\Column
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Export\Exportable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-low_on_stock_threshold')
             ->set_label(__('Low on Stock Threshold', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);

        $threshold_product = (int)$product->get_low_stock_amount();

        if ($threshold_product > 0) {
            return $threshold_product;
        }

        $threshold_global = (int)get_option('woocommerce_notify_low_stock_amount', 0);

        if ($threshold_global > 0) {
            return ac_helper()->html->tooltip(
                sprintf('<strong style="color:#ccc">%d</strong>', $threshold_global),
                sprintf(__('Set gobally to %d', 'codepress-admin-columns'), $threshold_global)
            );
        }

        return $this->get_empty_char();
    }

    public function export()
    {
        return new Export\Product\StockThreshold();
    }

    public function editing()
    {
        return new Editing\Product\StockThreshold();
    }

    public function search()
    {
        return new Search\Product\StockThreshold();
    }

}