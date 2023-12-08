<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Query\Bindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserMeta implements ACP\Sorting\Model\QueryBindings
{

    private $meta_field;

    public function __construct(string $meta_field)
    {
        $this->meta_field = $meta_field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('usermeta');

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $wpdb->usermeta AS $alias ON $wpdb->comments.user_id = $alias.user_id AND $alias.meta_key = %s",
                $this->meta_field
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}