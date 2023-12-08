<?php

namespace ACP\Query;

use ACP\Query;
use ACP\Query\Bindings\QueryArguments;
use WP_Query;

class Post extends Query
{

    /**
     * Register post callback functions
     */
    public function register(): void
    {
        add_filter('posts_where', [$this, 'cast_decimal_precision'], 20, 2);
        add_filter('posts_clauses', [$this, 'callback_clauses'], 20, 2);
        add_action('pre_get_posts', [$this, 'callback_meta_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_tax_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_mime_type_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_query_arguments'], 20);
    }

    public function callback_clauses(array $clauses, WP_Query $query): array
    {
        if ( ! $query->is_main_query()) {
            return $clauses;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $clauses['where'] .= "\nAND " . $binding->get_where();
            }
            if ($binding->get_join()) {
                $clauses['join'] .= "\n" . $binding->get_join();
            }
            if ($binding->get_order_by()) {
                $clauses['orderby'] = $binding->get_order_by();
            }
            if ($binding->get_group_by()) {
                $clauses['groupby'] = $binding->get_group_by();
            }
        }

        return $clauses;
    }

    /**
     * Add precision parameters to DECIMAL query
     *
     * @param string   $where
     * @param WP_Query $query
     *
     * @return string
     */
    public function cast_decimal_precision($where, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $where;
        }

        return str_replace('DECIMAL)', 'DECIMAL(10,2))', $where);
    }

    public function callback_meta_query(WP_Query $query): void
    {
        if ( ! $query->is_main_query()) {
            return;
        }

        $meta_query = $this->get_meta_query();

        if ( ! $meta_query) {
            return;
        }

        if ($query->get('meta_query')) {
            $meta_query[] = $query->get('meta_query');
        }

        $query->set('meta_query', $meta_query);
    }

    public function callback_tax_query(WP_Query $query): void
    {
        if ( ! $query->is_main_query()) {
            return;
        }

        $tax_query = [];

        foreach ($this->bindings as $binding) {
            if ($binding instanceof Query\Bindings\Post && $binding->get_tax_query()) {
                $tax_query[] = $binding->get_tax_query();
            }
        }

        $tax_query = array_filter($tax_query);

        if ( ! $tax_query) {
            return;
        }

        $tax_query['relation'] = 'AND';

        if ($query->get('tax_query')) {
            $tax_query[] = $query->get('tax_query');
        }

        $query->set('tax_query', $tax_query);
    }

    public function callback_mime_type_query(WP_Query $query): void
    {
        if ( ! $query->is_main_query()) {
            return;
        }

        $mime_types = [];

        foreach ($this->bindings as $binding) {
            if ($binding instanceof Query\Bindings\Media && $binding->get_mime_types()) {
                $mime_types = $binding->get_mime_types();
            }
        }

        $mime_types = array_filter($mime_types);

        if ( ! $mime_types) {
            return;
        }

        $query->set('post_mime_type', $mime_types);
    }

    public function callback_query_arguments(WP_Query $query): void
    {
        foreach ($this->bindings as $binding) {
            if ($binding instanceof QueryArguments) {
                foreach ($binding->get_query_arguments() as $query_var => $value) {
                    $query->set($query_var, $value);
                }
            }
        }
    }

}