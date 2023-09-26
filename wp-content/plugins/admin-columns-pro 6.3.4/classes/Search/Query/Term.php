<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use ACP\Search\Query\Bindings\QueryArguments;
use ACP\TermQueryInformation;
use WP_Term_Query;

class Term extends Query
{

    public function register(): void
    {
        add_action('pre_get_terms', [$this, 'callback_meta_query'], 1);
        add_action('pre_get_terms', [$this, 'callback_query_arguments'], 1);
        add_filter('terms_clauses', [$this, 'callback_clauses'], 1, 3);
    }

    public function callback_clauses($pieces, $taxonomies, $args)
    {
        if ( ! TermQueryInformation::is_main_query_by_args($args)) {
            return $pieces;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $pieces['where'] .= "\nAND " . $binding->get_where();
            }
            if ($binding->get_join()) {
                $pieces['join'] .= "\n" . $binding->get_join();
            }

            if ($binding->get_order_by()) {
                $pieces['orderby'] = "ORDER BY " . $binding->get_order_by();
                $pieces['order'] = false;
            }

            if ($binding->get_group_by()) {
                $pieces['orderby'] = "GROUP BY " . $binding->get_group_by() . "\n" . $pieces['orderby'];
            }
        }

        return $pieces;
    }

    public function callback_query_arguments(WP_Term_Query $query): void
    {
        if ( ! $this->is_main_query($query)) {
            return;
        }

        foreach ($this->bindings as $binding) {
            if ($binding instanceof QueryArguments) {
                foreach ($binding->get_query_arguments() as $query_var => $value) {
                    $query->query_vars[$query_var] = $value;
                }
            }
        }
    }

    public function callback_meta_query(WP_Term_Query $query): void
    {
        if ( ! $this->is_main_query($query)) {
            return;
        }

        $meta_query = $this->get_meta_query();

        if ( ! $meta_query) {
            return;
        }

        if ($query->query_vars['meta_query']) {
            $meta_query[] = $query->query_vars['meta_query'];
        }

        $query->query_vars['meta_query'] = $meta_query;
    }

    private function is_main_query(WP_Term_Query $query): bool
    {
        return TermQueryInformation::is_main_query($query);
    }

}