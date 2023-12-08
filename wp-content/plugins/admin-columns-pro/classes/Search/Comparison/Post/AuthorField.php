<?php

namespace ACP\Search\Comparison\Post;

use ACP\Helper\Select;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class AuthorField extends Comparison
{

    private $field;

    public function __construct(string $field)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
        ]);

        $this->field = $field;

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where = ComparisonFactory::create(
            "u.{$this->field}",
            $operator,
            $value
        );

        $subquery = $wpdb->prepare("SELECT u.ID FROM $wpdb->users AS u WHERE {$where()}");
        $alias = $bindings->get_unique_alias('usq');
        $bindings->join("JOIN($subquery) as $alias on $wpdb->posts.post_author = $alias.ID");

        return $bindings;
    }

}