<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class PostMeta implements QueryBindings
{

    private $meta_field;

    private $meta_key;

    public function __construct(string $meta_field, string $meta_key)
    {
        $this->meta_field = $meta_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias1 = $bindings->get_unique_alias('meta');
        $alias = $bindings->get_unique_alias('meta');

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN $wpdb->postmeta AS $alias1 ON $wpdb->posts.ID = $alias1.post_id AND $alias1.meta_key = %s 
			    LEFT JOIN $wpdb->postmeta AS $alias ON $alias1.meta_value = $alias.post_id AND $alias.meta_key = %s 
			    ",
                $this->meta_key,
                $this->meta_field
            )
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}