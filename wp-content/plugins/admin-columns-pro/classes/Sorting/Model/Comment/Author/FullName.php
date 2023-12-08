<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Query\Bindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class FullName implements ACP\Sorting\Model\QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_firstname = $bindings->get_unique_alias('fname');
        $alias_lastname = $bindings->get_unique_alias('lname');

        $bindings->join(
            "
			INNER JOIN $wpdb->usermeta AS $alias_firstname ON $wpdb->comments.user_id = $alias_firstname.user_id 
				AND $alias_firstname.meta_key = 'first_name'
			INNER JOIN $wpdb->usermeta AS $alias_lastname ON $wpdb->comments.user_id = $alias_lastname.user_id 
				AND $alias_lastname.meta_key = 'last_name'
		"
        );
        $bindings->group_by("$wpdb->comments.comment_ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                ["$alias_firstname.meta_value", "$alias_lastname.meta_value"],
                (string)$order
            )
        );

        return $bindings;
    }

}