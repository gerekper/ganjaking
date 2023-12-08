<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class Meta implements QueryBindings
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

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $wpdb->commentmeta AS acsort_commentmeta ON $wpdb->comments.comment_ID = acsort_commentmeta.comment_id AND acsort_commentmeta.meta_key = %s",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->comments.comment_ID");
        $bindings->order_by(
            $this->get_order_by($order) . sprintf(", $wpdb->comments.comment_ID %s", $order)
        );

        return $bindings;
    }

    protected function get_order_by(Order $order): string
    {
        return SqlOrderByFactory::create(
            "acsort_commentmeta.meta_value",
            (string)$order,
            [
                'cast_type' => (string)CastType::create_from_data_type($this->data_type),
            ]

        );
    }

}