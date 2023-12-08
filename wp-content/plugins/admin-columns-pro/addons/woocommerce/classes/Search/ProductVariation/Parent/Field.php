<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Field extends Comparison
{

    private $field;

    public function __construct(string $field, Operators $operators, string $value_type = null)
    {
        parent::__construct($operators, $value_type);

        $this->field = $field;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        $bindings = new Bindings();
        $post_alias = $bindings->get_unique_alias('post');

        $bindings->join("JOIN $wpdb->posts AS $post_alias on $wpdb->posts.post_parent = $post_alias.ID");
        $where = ComparisonFactory::create(
            $post_alias . '.' . $this->field,
            $operator,
            $value
        )->prepare();
        $bindings->where($where);

        return $bindings;
    }

}