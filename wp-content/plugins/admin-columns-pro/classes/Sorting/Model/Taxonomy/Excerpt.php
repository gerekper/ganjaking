<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Excerpt implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('texcerpt');

        $bindings->join("\nLEFT JOIN $wpdb->term_taxonomy AS $alias ON t.term_id = $alias.term_id");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.description",
                (string)$order
            )
        );

        return $bindings;
    }

}