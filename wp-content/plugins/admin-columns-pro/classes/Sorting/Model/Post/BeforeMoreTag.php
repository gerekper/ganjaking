<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;

class BeforeMoreTag implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $field = "$wpdb->posts.post_content";

        return (new Bindings())->order_by(
            sprintf(
                "CASE WHEN %s LIKE '%s' THEN 0 ELSE 1 END, %s %s",
                $field,
                '%--more--%',
                $field,
                $order
            )
        );
    }

}