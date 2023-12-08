<?php

namespace ACP\Sorting\Model\User\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserField implements QueryBindings
{

    private $field;

    private $meta_key;

    public function __construct(string $field, string $meta_key)
    {
        $this->field = $field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_meta = $bindings->get_unique_alias('ufield');
        $alias_user = $bindings->get_unique_alias('ufield');

        $bindings->join(
            $wpdb->prepare(
                "
			    LEFT JOIN $wpdb->usermeta AS $alias_meta ON $alias_meta.user_id = $wpdb->users.ID
				    AND $alias_meta.meta_key = %s
			    LEFT JOIN $wpdb->users AS $alias_user ON $alias_user.ID = $alias_meta.meta_value
		        ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias_user.`$this->field`", (string)$order)
        );

        return $bindings;
    }

}