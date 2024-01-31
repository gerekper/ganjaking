<?php

namespace ACP\Search\Comparison\User;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class UserPosts extends Comparison
{

    private $post_types;

    private $post_status;

    public function __construct(array $post_types, array $post_status)
    {
        $this->post_types = $post_types;
        $this->post_status = $post_status;

        parent::__construct(new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::LT,
            Operators::LTE,
            Operators::GT,
            Operators::GTE,
            Operators::BETWEEN,
        ]), Value::INT);
    }

    private function esc_sql_array($array)
    {
        return sprintf("'%s'", implode("','", array_map('esc_sql', $array)));
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('sq_posts');

        $sub_query = "SELECT COUNT(ID) as num_posts, post_author 
            FROM $wpdb->posts
            WHERE post_type IN ( " . $this->esc_sql_array($this->post_types) . ")
            AND post_status IN ( " . $this->esc_sql_array($this->post_status) . ")
            GROUP BY post_author";

        $bindings->join("LEFT JOIN ($sub_query) AS $alias ON $wpdb->users.ID = $alias.post_author");

        $bindings->where(ComparisonFactory::create("$alias.num_posts", $operator, $value)());

        return $bindings;
    }

}