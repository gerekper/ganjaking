<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class UserMeta implements QueryBindings
{

    private $meta_key;

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

        $alias = $bindings->get_unique_alias('usermeta');

        $bindings->join(
            $wpdb->prepare(
                "INNER JOIN $wpdb->usermeta AS $alias ON $wpdb->posts.post_author = $alias.user_id AND $alias.meta_key = %s",
                $this->meta_key
            )
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.meta_value",
                (string)$order,
                [
                    'cast_type' => $this->get_cast_type(),
                ]
            )
        );

        return $bindings;
    }

    private function get_cast_type(): ?CastType
    {
        return DataType::STRING !== (string)$this->data_type
            ? CastType::create_from_data_type($this->data_type)
            : null;
    }

}