<?php

declare(strict_types=1);

namespace ACA\WC\Helper;

use WC_Order;

final class User
{

    public function get_shop_order_totals_for_user(int $user_id, array $status = []): array
    {
        $totals = [];

        foreach ($this->get_shop_orders_by_user($user_id, $status) as $order) {
            if ( ! $order->get_total()) {
                continue;
            }

            $currency = $order->get_currency();

            if ( ! isset($totals[$currency])) {
                $totals[$currency] = 0;
            }

            $totals[$currency] += $order->get_total();
        }

        return $totals;
    }

    public function get_first_completed_order(int $user_id): ?WC_Order
    {
        return $this->get_extrema_completed_order($user_id);
    }

    public function get_last_completed_order(int $user_id): ?WC_Order
    {
        return $this->get_extrema_completed_order($user_id, 'max');
    }

    public function get_last_order(int $user_id, array $status): ?WC_Order
    {
        return $this->get_extrema_completed_order($user_id, 'max', $status);
    }

    public function get_extrema_completed_order(
        int $user_id,
        string $extrema = 'min',
        array $status = ['wc-completed']
    ): ?WC_Order {
        global $wpdb;

        $order = 'min' === $extrema
            ? 'ASC'
            : 'DESC';

        $where = '';

        if ($status) {
            $where = sprintf("AND wco.status IN ( '%s' )", implode("','", array_map('esc_sql', $status)));
        }

        $sql = $wpdb->prepare(
            "
            SELECT wco.id FROM {$wpdb->prefix}wc_orders AS wco
                INNER JOIN {$wpdb->prefix}wc_order_operational_data AS wcood ON wco.id = wcood.order_id
            WHERE wco.customer_id = %d
                $where
            GROUP BY wco.id
            ORDER BY wcood.date_completed_gmt $order
            LIMIT 1
            ",
            $user_id
        );

        $order_id = $wpdb->get_var($sql);

        if ( ! $order_id) {
            return null;
        }

        $order = wc_get_order($order_id);

        return $order instanceof WC_Order
            ? $order
            : null;
    }

    public function get_uniquely_sold_product_count(int $user_id): int
    {
        global $wpdb;

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $sql = $wpdb->prepare(
            "
            SELECT COUNT(wcopl.product_id)
            FROM {$wpdb->prefix}wc_orders AS wco
            LEFT JOIN {$wpdb->prefix}wc_order_product_lookup AS wcopl ON wcopl.order_id = wco.id
            WHERE wco.customer_id = %d
                AND wco.status IN $statuses_sql
        ",
            $user_id
        );

        return (int)$wpdb->get_var($sql);
    }

    public function get_sold_product_count(int $user_id): int
    {
        global $wpdb;

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $sql = $wpdb->prepare(
            "
            SELECT SUM(wcopl.product_qty)
            FROM {$wpdb->prefix}wc_orders AS wco
            LEFT JOIN {$wpdb->prefix}wc_order_product_lookup AS wcopl ON wcopl.order_id = wco.id
            WHERE wco.customer_id = %d
                AND wco.status IN $statuses_sql
        ",
            $user_id
        );

        return (int)$wpdb->get_var($sql);
    }

    public function get_total_sales(int $user_id): ?float
    {
        $spent = wc_get_customer_total_spent($user_id);

        return in_array($spent, ['0.00', '0,00'], true)
            ? null
            : (float)$spent;
    }

    public function get_shop_order_ids_by_user(int $user_id, array $status): array
    {
        $args = [
            'fields'         => 'ids',
            'post_type'      => 'shop_order',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'meta_query'     => [
                [
                    'key'   => '_customer_user',
                    'value' => (int)$user_id,
                ],
            ],
        ];

        if ($status) {
            $args['post_status'] = $status;
        }

        $order_ids = get_posts($args);

        if ( ! $order_ids) {
            return [];
        }

        return $order_ids;
    }

    public function get_shop_orders_by_user(int $user_id, array $status = ['wc-completed', 'wc-processing'])
    {
        $orders = [];

        foreach ($this->get_shop_order_ids_by_user($user_id, $status) as $order_id) {
            $orders[] = wc_get_order($order_id);
        }

        return $orders;
    }

}