<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Attachment extends AbstractModel implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('attachment');

        $bindings->join(
            "LEFT JOIN $wpdb->posts AS $alias ON $alias.post_parent = $wpdb->posts.ID AND $alias.post_type = 'attachment'"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.ID", (string)$order)
        );

        return $bindings;
    }

}