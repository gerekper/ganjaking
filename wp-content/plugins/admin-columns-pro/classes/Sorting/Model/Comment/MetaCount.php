<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

/**
 * Sort the comment list table on the number of times the meta_key is used by a comment.
 */
class MetaCount implements QueryBindings
{

    private $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('metacount');

        $bindings->join(
            $wpdb->prepare(
                "LEFT JOIN $wpdb->commentmeta AS $alias ON $wpdb->comments.comment_ID = $alias.comment_id AND $alias.meta_key = %s",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->comments.comment_ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("$alias.meta_key", (string)$order)
        );

        return $bindings;
    }

}
