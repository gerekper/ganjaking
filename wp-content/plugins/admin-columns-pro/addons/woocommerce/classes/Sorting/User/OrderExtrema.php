<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;
use InvalidArgumentException;

class OrderExtrema implements QueryBindings
{

    private $extrema;

    private $status;

    public function __construct(string $extrema = 'min', array $status = ['wc-completed'])
    {
        $this->extrema = $extrema;
        $this->status = $status;
        $this->validate();
    }

    private function validate(): void
    {
        if ( ! in_array($this->extrema, ['min', 'max'], true)) {
            throw new InvalidArgumentException('Invalid extrema');
        }
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where_status = $this->status
            ? sprintf(
                " AND acsort_orders.status IN ( '%s' )",
                implode("','", array_map('esc_sql', $this->status))
            )
            : '';

        $alias = $bindings->get_unique_alias('order_extrema');

        $bindings->group_by("$wpdb->users.ID");
        $bindings->join(
            "
            LEFT JOIN {$wpdb->prefix}wc_orders AS acsort_orders ON acsort_orders.customer_id = $wpdb->users.ID
                $where_status
            LEFT JOIN {$wpdb->prefix}wc_order_operational_data AS $alias ON $alias.order_id = acsort_orders.id
        "
        );

        $computation_type = 'min' === $this->extrema
            ? ComputationType::MIN
            : ComputationType::MAX;

        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType($computation_type),
                "$alias.date_completed_gmt",
                (string)$order
            )
        );

        return $bindings;
    }

}