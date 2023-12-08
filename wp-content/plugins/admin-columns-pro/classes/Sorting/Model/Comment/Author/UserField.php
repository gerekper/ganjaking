<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment\Author;

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

        $alias = $bindings->get_unique_alias('ufield');

        $bindings->join("LEFT JOIN $wpdb->users AS $alias ON $wpdb->comments.user_id = $alias.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.$this->user_field", (string)$order)
        );

        return $bindings;
    }

}