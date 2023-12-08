<?php

declare(strict_types=1);

namespace ACA\WC\Helper\Order;

use AC\Helper\Select\Options;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Scheme\Orders;
use DateTime;
use InvalidArgumentException;

class DateOptionsFactory
{

    public function create_operational_data_options(string $field): Options
    {
        if ( ! in_array($field, [
            OrderOperationalData::DATE_COMPLETED_GMT,
            OrderOperationalData::DATE_PAID_GMT,
        ], true)) {
            throw new InvalidArgumentException('Invalid date field.');
        }

        global $wpdb;

        $date_column = esc_sql($field);

        $sql =
            "
            SELECT DATE_FORMAT(ood.$date_column,'%Y%m')
            FROM {$wpdb->prefix}wc_orders AS o
                JOIN {$wpdb->prefix}wc_order_operational_data AS ood ON ood.order_id = o.id
            WHERE 
                ood.$date_column is not null
            ";

        return $this->convert_dates($wpdb->get_col($sql));
    }

    public function create_orders_options(string $field): Options
    {
        if ( ! in_array($field, [
            Orders::DATE_CREATED_GMT,
            Orders::DATE_UPDATED_GMT,
        ], true)) {
            throw new InvalidArgumentException('Invalid date field.');
        }

        global $wpdb;

        $date_column = esc_sql($field);

        $sql =
            "
            SELECT DATE_FORMAT(o.$date_column,'%Y%m')
            FROM {$wpdb->prefix}wc_orders AS o
            WHERE 
                o.$date_column is not null
            ";

        return $this->convert_dates($wpdb->get_col($sql));
    }

    private function convert_dates(array $rows): Options
    {
        $options = [];
        foreach ($rows as $value) {
            $date = DateTime::createFromFormat('Ym', $value);

            if ( ! $date) {
                continue;
            }

            $options[$date->format('Ym')] = $date->format('F Y');
        }

        krsort($options);

        return Options::create_from_array($options);
    }

}