<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;

class LowOnStock extends AC\Column
    implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Export\Exportable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-low_on_stock')
             ->set_label(__('Low on Stock', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);

        $is_managing_stock = $product->managing_stock();

        if ( ! $is_managing_stock) {
            return $this->get_empty_char();
        }

        $threshold_product = (int)$product->get_low_stock_amount();
        $threshold_global = (int)get_option('woocommerce_notify_low_stock_amount', 0);
        $has_threshold = $threshold_product > 0 || $threshold_global > 0;

        if ( ! $has_threshold) {
            return $this->get_empty_char();
        }

        $threshold = $threshold_product ?: $threshold_global;
        $stock = (int)$product->get_stock_quantity();

        $label = sprintf(
            '<strong style="color: #eaa600">%s</strong> (%d)',
            __('Low On Stock', 'codepress-admin-columns'),
            $product->get_stock_quantity()
        );

        if ($stock <= $threshold) {
            return ac_helper()->html->tooltip(
                $label,
                sprintf(
                    __('Current stock (%d) is below threshold (%d).', 'codepress-admnin-columns'),
                    $stock,
                    $threshold
                )
            );
        }

        return $this->get_empty_char();
    }

    public function export()
    {
        return new Export\Product\LowOnStock();
    }

    public function search()
    {
        return new Search\Product\LowOnStock();
    }

}