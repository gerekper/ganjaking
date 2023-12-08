<?php

namespace ACA\WC\Column\Product\ShopOrder;

use AC;
use ACA\WC\Settings\OrderStatuses;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\IntegerFormattableTrait;

class Sales extends AC\Column implements Formattable
{

    use IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-product_sales')
             ->set_label(__('Products Sold', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($product_id)
    {
        $value = $this->get_raw_value($product_id);

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $value;
    }

    protected function get_order_statuses(): array
    {
        $setting = $this->get_setting(OrderStatuses::NAME);

        return $setting instanceof OrderStatuses
            ? $setting->get_order_status()
            : ['wc-completed'];
    }

    protected function register_settings(): void
    {
        parent::register_settings();

        $this->add_setting(new OrderStatuses($this, ['wc-completed']));
    }

    public function get_raw_value($product_id)
    {
        global $wpdb;

        $status = apply_filters('acp/wc/column/product/sales/statuses', $this->get_order_statuses(), $this);
        $status_in = sprintf(
            "'%s'",
            implode("','", array_map('esc_sql', $status))
        );

        $sql = "
			SELECT
			    SUM( oim_q.meta_value )
			FROM 
			    {$wpdb->prefix}woocommerce_order_itemmeta AS oim_pid
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON oim_pid.order_item_id = oi.order_item_id
			INNER JOIN $wpdb->posts AS p ON p.ID = oi.order_id
				AND p.post_status IN( $status_in )
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim_q ON oim_q.order_item_id = oi.order_item_id 
				AND oim_q.meta_key = '_qty'
			WHERE oim_pid.meta_key IN ( '_product_id', '_variation_id' ) 
	        AND oim_pid.meta_value = %s
	   	";

        return $wpdb->get_var($wpdb->prepare($sql, $product_id));
    }

}