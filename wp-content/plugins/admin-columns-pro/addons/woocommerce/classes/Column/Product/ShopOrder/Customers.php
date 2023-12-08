<?php

namespace ACA\WC\Column\Product\ShopOrder;

use AC;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 3.0
 */
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

    public function get_raw_value($id)
    {
        global $wpdb;

        $post_status = 'wc-completed';

        $sql = "
			SELECT DISTINCT pm.meta_value AS cid
			FROM $wpdb->postmeta AS pm
			INNER JOIN $wpdb->posts AS p
				ON p.ID = pm.post_id AND p.post_status = %s
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi
				ON oi.order_id = p.ID AND oi.order_item_type = 'line_item'
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
				ON oi.order_item_id = oim.order_item_id AND oim.meta_key = '_product_id'
			WHERE pm.meta_key = '_customer_user'
			AND oim.meta_value = %d
		";

        return $wpdb->get_col($wpdb->prepare($sql, [$post_status, $id]));
    }

    public function get_value($id)
    {
        return count($this->get_raw_value($id)) ?: $this->get_empty_char();
    }

    public function sorting()
    {
        return new Sorting\Product\ShopOrder\Customers();
    }

}