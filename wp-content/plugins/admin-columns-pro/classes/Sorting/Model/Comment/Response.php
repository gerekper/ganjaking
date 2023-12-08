<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Response implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('response');

        $bindings->join("INNER JOIN $wpdb->posts $alias ON $alias.ID = $wpdb->comments.comment_post_ID ");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.post_title ", (string)$order)
        );

        return $bindings;
    }

}