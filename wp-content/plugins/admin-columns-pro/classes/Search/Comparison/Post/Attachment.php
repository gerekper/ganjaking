<?php

namespace ACP\Search\Comparison\Post;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Attachment extends Comparison
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            null,
            new Labels([
                Operators::IS_EMPTY     => __('Has No Attachment', 'codepress-admin-columns'),
                Operators::NOT_IS_EMPTY => __('Has Attachment', 'codepress-admin-columns'),
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('attachment');

        switch ($operator) {
            case  Operators::NOT_IS_EMPTY :
                $bindings->join(
                    "JOIN $wpdb->posts AS $alias ON $alias.post_parent = $wpdb->posts.ID AND $alias.post_type = 'attachment'"
                );
                break;
            case Operators::IS_EMPTY :
                $bindings->join(
                    "LEFT JOIN $wpdb->posts AS $alias ON $alias.post_parent = $wpdb->posts.ID AND $alias.post_type = 'attachment'"
                )
                         ->where("$alias.ID is NULL");
                break;
        }

        return $bindings;
    }

}