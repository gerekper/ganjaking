<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use ACP\Search\Query\Bindings\QueryArguments;
use WP_Query;

class Post extends Query
{

    /**
     * Register post callback functions
     */
    public function register(): void
    {
        add_filter('posts_where', [$this, 'cast_decimal_precision'], 20, 2);
        add_filter('posts_where', [$this, 'callback_where'], 20, 2);
        add_filter('posts_join', [$this, 'callback_join'], 20, 2);
        add_filter('posts_groupby', [$this, 'callback_group_by'], 20, 2);
        add_filter('posts_orderby', [$this, 'callback_order_by'], 20, 2);
        add_action('pre_get_posts', [$this, 'callback_meta_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_tax_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_mime_type_query'], 20);
        add_action('pre_get_posts', [$this, 'callback_query_arguments'], 20);
    }

    /**
     * Add precision parameters to DECIMAL query
     *
     * @param string   $where
     * @param WP_Query $query
     *
     * @return string
     */
    function cast_decimal_precision($where, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $where;
        }

        return str_replace('DECIMAL', 'DECIMAL(10,2)', $where);
    }

    /**
     * Function to serve the template functions that need this hook
     *
     * @param string   $where
     * @param WP_Query $query
     *
     * @return string
     */
    public function callback_where($where, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $where;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $where .= "\nAND " . $binding->get_where();
            }
        }

        return $where;
    }

    public function callback_join($join, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $join;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_join()) {
                $join .= "\n" . $binding->get_join();
            }
        }

        return $join;
    }

    public function callback_group_by($group_by, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $group_by;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_group_by()) {
                $group_by = $binding->get_group_by();
                break;
            }
        }

        return $group_by;
    }

    public function callback_order_by($order_by, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $order_by;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_order_by()) {
                $order_by = $binding->get_order_by();
                break;
            }
        }

        return $order_by;
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