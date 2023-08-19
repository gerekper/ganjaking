<?php

namespace ACA\WC\Column\Product;

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
        return $this->get_raw_value($product_id) ?: $this->get_empty_char();
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
        $status_statement = '';

        if ( ! empty($status)) {
            $status_statement = sprintf(
                "AND wco.status IN ('%s')",
                implode("','", array_map('esc_sql', $status))
            );
        }

        $sql = $wpdb->prepare(
            "
            SELECT SUM( wcopl.product_qty )
            FROM {$wpdb->prefix}wc_order_product_lookup as wcopl
            JOIN {$wpdb->prefix}wc_orders as wco 
                ON wcopl.order_id = wco.ID {$status_statement}
            WHERE wcopl.product_id = %d OR wcopl.variation_id = %d
        ",
            $product_id,
            $product_id
        );

        return $wpdb->get_var($sql);
    }

}