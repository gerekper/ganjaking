<?php

namespace ACP\Search\Comparison\Media;

use AC;
use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class UsedAsFeaturedImage extends Comparison implements Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('subquery');

        $sub_query = "SELECT DISTINCT( meta_value ) as ID
			FROM $wpdb->postmeta
			WHERE meta_key = '_thumbnail_id'";

        if ('true' === $value->get_value()) {
            $bindings->join("INNER JOIN ($sub_query) AS $alias ON $wpdb->posts.ID = $alias.ID ");
        } else {
            $bindings->join("LEFT JOIN ($sub_query) AS $alias ON $wpdb->posts.ID = $alias.ID ");
            $bindings->where("$alias.ID IS NULL");
        }

        return $bindings;
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            'true'  => __('In use', 'codepress-admin-columns'),
            'false' => __('Not used', 'codepress-admin-columns'),
        ]);
    }

}