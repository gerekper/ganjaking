<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Sorting;
use ACP;

class Customers extends AC\Column
    implements ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-product_customers')
             ->set_label(__('Customers', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        return $this->get_raw_value($id) ?: $this->get_empty_char();
    }

    public function get_raw_value($product_id)
    {
        global $wpdb;

        $post_status = 'wc-completed';

        $sql = $wpdb->prepare(
            "
            SELECT COUNT( * )
            FROM {$wpdb->prefix}wc_orders as o 
            JOIN {$wpdb->prefix}wc_order_product_lookup opl
                ON o.id = opl.order_id AND opl.product_id = %d
            WHERE
                o.type = 'shop_order'
                AND o.status = %s
            ORDER BY o.customer_id
        ",
            $product_id,
            $post_status
        );

        return $wpdb->get_var($sql);
    }

    public function sorting()
    {
        return new Sorting\Product\Order\Customers();
    }

}