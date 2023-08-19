<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Search;
use ACP;

class OrderCount extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-order_count')
             ->set_label(__('Orders', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($post_id)
    {
        return $this->get_raw_value($post_id) ?: $this->get_empty_char();
    }

    public function get_raw_value($post_id)
    {
        global $wpdb;

        $num_orders = $wpdb->get_var(
            $wpdb->prepare(
                "
			    SELECT COUNT( * )
			    FROM {$wpdb->prefix}wc_orders wc_o
			    JOIN {$wpdb->prefix}wc_order_product_lookup wc_opl ON wc_o.ID = wc_opl.order_id AND wc_opl.product_id = %d
			    ",
                $post_id
            )
        );

        return $num_orders;
    }

    public function search()
    {
        return new Search\Product\Order\OrderCount();
    }

}