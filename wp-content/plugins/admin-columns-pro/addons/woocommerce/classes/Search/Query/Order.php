<?php

namespace ACA\WC\Search\Query;

use ACP\Search;
use ACP\Search\Query\Bindings\QueryArguments;

final class Order extends Search\Query
{

    public function register(): void
    {
        add_filter('woocommerce_orders_table_query_clauses', [$this, 'parse_sql_clauses']);
        add_filter('woocommerce_order_list_table_prepare_items_query_args', [$this, 'parse_order_meta_query'], 9);
        add_filter('woocommerce_order_list_table_prepare_items_query_args', [$this, 'parse_order_arguments']);
    }

    public function parse_sql_clauses(array $clauses): array
    {
        foreach ($this->bindings as $binding) {
            if ($binding->get_join()) {
                $clauses['join'] .= "\n" . $binding->get_join();
            }
            if ($binding->get_where()) {
                $clauses['where'] .= "\nAND " . $binding->get_where();
            }
            if ($binding->get_group_by()) {
                $clauses['groupby'] = $binding->get_group_by();
            }
            if ($binding->get_order_by()) {
                $clauses['orderby'] = $binding->get_order_by();
            }
        }

        return $clauses;
    }

    public function parse_order_meta_query(array $args): array
    {
        if ( ! isset ($args['meta_query'])) {
            $args['meta_query'] = [];
        }

        foreach ($this->bindings as $binding) {
            $meta_query = $binding->get_meta_query();

            if ($meta_query) {
                $args['meta_query'][] = $meta_query;
            }
        }

        return $args;
    }

    public function parse_order_arguments(array $args): array
    {
        $query_args = [];

        foreach ($this->bindings as $binding) {
            if ($binding instanceof QueryArguments) {
                $query_args[] = $binding->get_query_arguments();
            }
        }

        return array_merge($args, ...$query_args);
    }

}