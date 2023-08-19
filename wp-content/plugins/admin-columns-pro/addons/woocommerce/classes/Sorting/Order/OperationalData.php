<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class OperationalData extends AbstractModel implements QueryBindings
{

    private $field;

    public function __construct(string $field, DataType $data_type = null)
    {
        parent::__construct();

        $this->field = $field;
        $this->data_type = $data_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_operational_data = $wpdb->prefix . 'wc_order_operational_data';

        $bindings->join(
            sprintf(
                "LEFT JOIN %s AS acsort_od ON acsort_od.order_id = %s.id",
                esc_sql($table_operational_data),
                esc_sql($table_orders)
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "acsort_od.$this->field",
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
            case DataType::NUMERIC:
                return [null, 0];
            default:
                return [null, ''];
        }
    }

}