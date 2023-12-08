<?php

namespace ACP\Sorting\Model\Post;

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

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->posts.ID = acsort_postmeta.post_id
                    AND acsort_postmeta.meta_key = %s
                ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by($this->get_order_by($order));

        return $bindings;
    }

    protected function get_order_by(Order $order): string
    {
        $cast_type = DataType::STRING !== (string)$this->data_type
            ? (string)CastType::create_from_data_type($this->data_type)
            : null;

        return SqlOrderByFactory::create(
            "acsort_postmeta.`meta_value`",
            (string)$order,
            [
                'cast_type' => $cast_type,
            ]
        );
    }

}