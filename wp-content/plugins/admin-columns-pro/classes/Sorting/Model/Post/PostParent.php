<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class PostParent implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('pparent');

        $bindings->join("LEFT JOIN $wpdb->posts AS $alias ON $wpdb->posts.post_parent = $alias.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.post_title", (string)$order)
        );

        return $bindings;
    }

}