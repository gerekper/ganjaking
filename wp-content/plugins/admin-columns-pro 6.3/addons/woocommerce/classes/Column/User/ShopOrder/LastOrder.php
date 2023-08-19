<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC\Column;
use ACA\WC\Settings;
use ACA\WC\Settings\OrderStatuses;
use ACA\WC\Sorting;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;
use ACP\Sorting\Sortable;

class LastOrder extends Column implements Sortable, Exportable
{

    public function __construct()
    {
        $this->set_type('column-wc-user-last_order')
             ->set_group('woocommerce')
             ->set_label(__('Last Order', 'codepress-admin-columns'));
    }

    protected function get_last_order($user_id)
    {
        global $wpdb;

        $where_status = '';

        $status = $this->get_order_statuses();

        if ($status) {
            $where_status = sprintf(" AND pp.post_status IN ( '%s' )", implode("','", array_map('esc_sql', $status)));
        }

        $sql = $wpdb->prepare(
            "
		SELECT
			pp.ID
		FROM
			$wpdb->posts AS pp
			INNER JOIN $wpdb->postmeta AS pm ON pp.ID = pm.post_id AND pm.meta_key = '_customer_user'
		WHERE
		  	1=1
			AND pm.meta_value = %d
			AND pp.post_type IN ( 'shop_order', 'shop_order_refund' )
			$where_status
		ORDER BY pp.post_date DESC
		LIMIT 1
		",
            (int)$user_id
        );

        $order_id = $wpdb->get_var($sql);

        if ( ! $order_id) {
            return null;
        }

        return wc_get_order($order_id) ?: null;
    }

    private function get_order_statuses(): array
    {
        $setting = $this->get_setting(OrderStatuses::NAME);

        return $setting instanceof OrderStatuses
            ? $setting->get_order_status()
            : [];
    }

    public function get_value($user_id)
    {
        $order = $this->get_last_order($user_id);

        if ( ! $order) {
            return $this->get_empty_char();
        }

        return $this->get_setting(Settings\User\Order::NAME)->format($order, $order);
    }

    public function get_raw_value($id)
    {
        return $this->get_last_order($id);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\OrderStatuses($this));
        $this->add_setting(new Settings\User\Order($this));
    }

    public function sorting()
    {
        return new Sorting\User\ShopOrder\LastOrder();
    }

    public function export()
    {
        return new StrippedValue($this);
    }

}