<?php

namespace ACA\WC\Column\Product\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;

/**
 * @since 1.1
 */
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

        if ( ! $price) {
            return $this->get_empty_char();
        }

        return wc_price($price);
    }

    public function get_raw_value($post_id)
    {
        global $wpdb;

        $num_orders = $wpdb->get_var(
            $wpdb->prepare(
                "
			SELECT
				SUM( wc_oim2.meta_value )
			FROM
				{$wpdb->prefix}woocommerce_order_items wc_oi
			JOIN
				{$wpdb->prefix}woocommerce_order_itemmeta wc_oim
				ON
					wc_oi.order_item_id = wc_oim.order_item_id
			JOIN
				{$wpdb->prefix}woocommerce_order_itemmeta wc_oim2
				ON
					wc_oi.order_item_id = wc_oim2.order_item_id
			WHERE
				wc_oim.meta_key = '_product_id'
				AND
				wc_oim.meta_value = %d
				AND
				wc_oim2.meta_key = '_line_total'
			",
                $post_id
            )
        );

        if ( ! $num_orders) {
            return false;
        }

        return $num_orders;
    }

}