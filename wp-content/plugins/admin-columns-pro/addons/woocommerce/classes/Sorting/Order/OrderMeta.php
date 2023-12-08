<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class OrderMeta implements QueryBindings
{

    private $meta_field;

    private $data_type;

    public function __construct(string $meta_field, DataType $data_type = null)
    {
        $this->meta_field = $meta_field;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        if (DataType::DATETIME === $this->data_type->get_value()) {
            $order = new Order('ASC' === (string)$order ? 'DESC' : 'ASC');
        }

        $alias = $bindings->get_unique_alias('wcs_meta');

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_addresses = $wpdb->prefix . 'wc_orders_meta';

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $table_addresses AS $alias ON $alias.order_id = $table_orders.id AND $alias.meta_key = %s",
                $this->meta_field
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.meta_value",
                (string)$order,
                [
                    'cast_type' => CastType::create_from_data_type($this->data_type)->get_value(),
                ]
            )
        );

        return $bindings;
    }

}