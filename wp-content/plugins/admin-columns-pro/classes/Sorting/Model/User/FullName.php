<?php

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class FullName implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_meta1 = $bindings->get_unique_alias('fullname');
        $alias_meta2 = $bindings->get_unique_alias('fullname');

        $bindings->join(
            "
			INNER JOIN $wpdb->usermeta AS $alias_meta1 ON $wpdb->users.ID = $alias_meta1.user_id
				AND $alias_meta1.meta_key = 'first_name'
			INNER JOIN $wpdb->usermeta AS $alias_meta2 ON $wpdb->users.ID = $alias_meta2.user_id
				AND $alias_meta2.meta_key = 'last_name'
		    "
        );

        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                [
                    "$alias_meta1.meta_value",
                    "$alias_meta2.meta_value",
                ],
                (string)$order
            ) . ", $wpdb->users.ID " . $order
        );

        return $bindings;
    }

}