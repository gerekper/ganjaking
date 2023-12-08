<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class Meta implements QueryBindings
{

    protected $meta_key;

    protected $data_type;

    public function __construct(string $meta_key, DataType $data_type = null)
    {
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('meta');

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $wpdb->termmeta AS $alias ON t.term_id = $alias.term_id AND $alias.meta_key = %s",
                $this->meta_key
            )
        );

        $bindings->group_by("t.term_id");
        $bindings->order_by($this->get_order_by($alias, $order));

        return $bindings;
    }

    protected function get_order_by(string $alias, Order $order): string
    {
        return SqlOrderByFactory::create(
            "$alias.`meta_value`",
            (string)$order,
            [
                'cast_type' => (string)CastType::create_from_data_type($this->data_type),
            ]
        );
    }

}