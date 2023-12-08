<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class CustomerField implements QueryBindings
{

    private $field;

    protected $data_type;

    public function __construct(string $field, DataType $data_type = null)
    {
        $this->field = $field;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('wcs_cf');

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_customer = $wpdb->prefix . 'wc_customer_lookup';

        $bindings->join(
            sprintf(
                "\nLEFT JOIN %s AS $alias ON $alias.user_id = %s.customer_id",
                esc_sql($table_customer),
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
            case DataType::NUMERIC:
                return [null, 0];
            default:
                return [null, ''];
        }
    }

}