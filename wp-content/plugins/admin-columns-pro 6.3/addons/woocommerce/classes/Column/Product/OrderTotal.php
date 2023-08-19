<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;

class OrderTotal extends AC\Column implements Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-total_order_amount')
             ->set_label(__('Total Revenue', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    public function get_value($post_id)
    {
        $price = $this->get_raw_value($post_id);

        return $price
            ? wc_price($price)
            : $this->get_empty_char();
    }

    public function get_raw_value($post_id)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
                SELECT SUM( wcopl.product_net_revenue )
                FROM {$wpdb->prefix}wc_order_product_lookup as wcopl
                WHERE wcopl.product_id = %d             
            ",
            $post_id
        );

        return $wpdb->get_var($sql);
    }

}