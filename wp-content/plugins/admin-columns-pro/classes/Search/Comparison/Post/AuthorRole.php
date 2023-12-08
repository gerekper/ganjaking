<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class AuthorRole extends Comparison implements Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        
        $bindings = new Bindings();

        $alias_user = $bindings->get_unique_alias('user');
        $alias_usermeta = $bindings->get_unique_alias('usermeta');

        $bindings->join(
            "
            INNER JOIN $wpdb->users AS $alias_user ON $wpdb->posts.post_author = $alias_user.ID
            INNER JOIN $wpdb->usermeta AS $alias_usermeta ON $alias_user.ID = $alias_usermeta.user_id
                AND $alias_usermeta.meta_key = 'wp_capabilities'
            "
        );

        $value = new Value(
            serialize($value->get_value()),
            $value->get_type()
        );

        $where = ComparisonFactory::create(
            $alias_usermeta . '.meta_value',
            Operators::CONTAINS,
            $value
        )->prepare();

        $bindings->where($where);

        return $bindings;
    }

    public function get_values(): Options
    {
        $options = [];

        foreach (wp_roles()->roles as $key => $role) {
            $options[$key] = translate_user_role($role['name']);
        }

        asort($options);

        return Options::create_from_array($options);
    }

}