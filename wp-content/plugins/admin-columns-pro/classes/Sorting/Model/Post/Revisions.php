<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Revisions extends AbstractModel implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        return $bindings->join("LEFT JOIN $wpdb->posts AS acs_p ON acs_p.post_parent = $wpdb->posts.ID")
                        ->group_by("$wpdb->posts.ID")
                        ->order_by(SqlOrderByFactory::create_with_count("$wpdb->posts.ID", (string)$order));
    }

}