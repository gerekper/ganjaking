<?php

namespace ACP\Sorting\Model\User\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class PostField implements QueryBindings
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
        $alias = $bindings->get_unique_alias('umeta');
        $alias_post = $bindings->get_unique_alias('pfield');

        $bindings->join(
            $wpdb->prepare(
                "
			    LEFT JOIN $wpdb->usermeta AS $alias ON $alias.user_id = $wpdb->users.ID
				    AND $alias.meta_key = %s
			    LEFT JOIN $wpdb->posts AS $alias_post ON $alias_post.ID = $alias.meta_value
		        ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias_post.$this->field", (string)$order)
        );

        return $bindings;
    }

}