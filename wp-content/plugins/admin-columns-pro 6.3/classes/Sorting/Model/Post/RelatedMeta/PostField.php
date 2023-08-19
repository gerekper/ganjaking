<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class PostField extends AbstractModel implements QueryBindings
{

    private $post_field;

    private $meta_key;

    public function __construct(string $post_field, string $meta_key)
    {
        parent::__construct();

        $this->post_field = $post_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('post');

        $join = $wpdb->prepare(
            "
			LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = %s
			LEFT JOIN $wpdb->posts AS $alias ON $alias.ID = $wpdb->postmeta.meta_value
			",
            $this->meta_key
        );

        $bindings->join($join);
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.$this->post_field",
                (string)$order
            )
        );

        return $bindings;
    }

}