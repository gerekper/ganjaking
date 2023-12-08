<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class PostField implements QueryBindings
{

    private $post_field;

    private $meta_key;

    public function __construct(string $post_field, string $meta_key)
    {
        $this->post_field = $post_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $post_alias = $bindings->get_unique_alias('post');
        $postmeta_alias = $bindings->get_unique_alias('postmeta');

        $join = $wpdb->prepare(
            "
			LEFT JOIN $wpdb->postmeta AS $postmeta_alias ON $wpdb->posts.ID = $postmeta_alias.post_id AND $postmeta_alias.meta_key = %s
			LEFT JOIN $wpdb->posts AS $post_alias ON $post_alias.ID = $postmeta_alias.meta_value
			",
            $this->meta_key
        );

        $bindings->join($join);
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$post_alias.$this->post_field",
                (string)$order
            )
        );

        return $bindings;
    }

}