<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class OrderData extends AbstractModel implements QueryBindings
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

        $field = sprintf('%s.%s', $wpdb->prefix . 'wc_orders', $this->field);

        $bindings->order_by(
            SqlOrderByFactory::create(
                $field,
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