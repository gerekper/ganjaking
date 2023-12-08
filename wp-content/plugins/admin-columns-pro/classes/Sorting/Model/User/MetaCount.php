<?php

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

/**
 * Sort a user list table on the number of times the meta_key is used by a user.
 */
class MetaCount implements QueryBindings
{

    protected $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('mcount');

        $bindings->join(
            $wpdb->prepare(
                "
			    LEFT JOIN $wpdb->usermeta AS $alias ON $wpdb->users.ID = $alias.user_id
				    AND $alias.meta_key = %s
		        ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::COUNT),
                "$alias.meta_key",
                (string)$order,
                true
            )
        );

        return $bindings;
    }

}