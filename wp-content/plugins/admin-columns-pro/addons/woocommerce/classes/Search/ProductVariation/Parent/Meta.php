<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Meta extends Comparison
{

    protected $meta_key;

    public function __construct(string $meta_key, Operators $operators, $value_type = null)
    {
        parent::__construct($operators, $value_type);

        $this->meta_key = $meta_key;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        $bindings = new Bindings();
        $post_alias = $bindings->get_unique_alias('post');
        $meta_alias = $bindings->get_unique_alias('postmeta');

        $bindings->join(
            $wpdb->prepare(
                "
                JOIN $wpdb->posts AS $post_alias ON $wpdb->posts.post_parent = $post_alias.ID
                JOIN $wpdb->postmeta AS $meta_alias ON $post_alias.ID = $meta_alias.post_id AND $meta_alias.meta_key = %s 
            ",
                $this->meta_key
            )
        );

        $where = ComparisonFactory::create(
            $meta_alias . '.meta_value',
            $operator,
            $value
        )->prepare();
        $bindings->where($where);

        return $bindings;
    }

}