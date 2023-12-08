<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserField implements QueryBindings
{

    private $user_field;

    public function __construct(string $user_field)
    {
        $this->user_field = $user_field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('userfield');

        $bindings->join("INNER JOIN $wpdb->users AS $alias ON $wpdb->posts.post_author = $alias.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                sprintf("$alias.%s", $this->user_field),
                (string)$order
            )
        );

        return $bindings;
    }

}