<?php

namespace ACP\Search\Comparison\Post;

use ACP\Helper\Select;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class AuthorMeta extends Comparison
{

    private $meta_key;

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
        ]);

        $this->meta_key = $meta_key;

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where = ComparisonFactory::create(
            "um.meta_value",
            $operator,
            $value
        );

        $subquery = $wpdb->prepare(
            "
            SELECT u.ID FROM $wpdb->users as u
                JOIN $wpdb->usermeta as um on u.ID = um.user_id AND um.meta_key = %s AND {$where()}
        ",
            $this->meta_key
        );

        $alias = $bindings->get_unique_alias('umsq');
        $bindings->join("JOIN($subquery) as $alias on $wpdb->posts.post_author = $alias.ID");

        return $bindings;
    }

}