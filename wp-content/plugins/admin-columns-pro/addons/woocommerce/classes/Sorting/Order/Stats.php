<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;

class Stats implements QueryBindings
{

    private $field;

    private $data_type;

    public function __construct(string $field, DataType $data_type = null)
    {
        $this->field = $field;
        $this->data_type = $data_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_stats = $wpdb->prefix . 'wc_order_stats';

        $alias = $bindings->get_unique_alias('acsort');

        $bindings->join(
            sprintf(
                "LEFT JOIN %s AS $alias ON $alias.order_id = %s.id",
                esc_sql($table_stats),
                esc_sql($table_orders)
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.$this->field",
                (string)$order,
                [
                    'empty_values' => $this->get_empty_values(),
                ]
            )
        );

        return $bindings;
    }

    protected function get_empty_values(): array
    {
        switch ($this->data_type) {
            case DataType::DATE:
                return [EmptyValues::NULL, EmptyValues::ZERO];
            case DataType::NUMERIC:
                return [EmptyValues::NULL, EmptyValues::LTE_ZERO];
            default:
                return [EmptyValues::NULL, EmptyValues::EMPTY_STRING];
        }
    }

}