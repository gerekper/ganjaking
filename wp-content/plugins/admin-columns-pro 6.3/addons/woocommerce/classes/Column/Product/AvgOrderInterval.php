<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\ConditionalFormat;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;
use DateTime;

class AvgOrderInterval extends AC\Column implements Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-avg_order_interval')
             ->set_label(__('Average Order Interval', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(
            new ConditionalFormat\Formatter\Product\AvgOrderIntervalFormatter()
        );
    }

    public function get_value($post_id)
    {
        $days = $this->get_raw_value($post_id);

        return $days
            ? human_time_diff(0, $days * DAY_IN_SECONDS)
            : $this->get_empty_char();
    }

    public function get_raw_value($post_id)
    {
        $orders = $this->get_product_order_count($post_id);

        return $orders
            ? round($this->get_period_in_days() / $orders, 3)
            : false;
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Product\Period($this));
    }

    public function get_product_order_count($post_id): int
    {
        global $wpdb;

        $start_date = new DateTime();
        $start_date->modify('-' . $this->get_period_in_days() . 'days');

        $sql = "SELECT COUNT(*) as count
            FROM {$wpdb->prefix}wc_orders as o
            JOIN {$wpdb->prefix}wc_order_product_lookup as op
                ON o.id = op.order_id AND op.product_id = ${post_id}
            WHERE 
                o.date_created_gmt >= {$start_date->format('Y-m-d')}
                AND o.type = 'shop_order'
                AND o.status = 'wc-completed'
            ";

        $count = $wpdb->get_var($sql);

        return $count ? (int)$count : 0;
    }

    private function get_period_in_days()
    {
        $setting = $this->get_setting('period');

        return $setting instanceof Settings\Product\Period
            ? $setting->get_period()
            : false;
    }

}