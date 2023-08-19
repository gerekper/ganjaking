<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use ACP\Search\Query\Bindings\QueryArguments;
use WP_User_Query;

class User extends Query
{

    public function register(): void
    {
        add_filter('users_list_table_query_args', [$this, 'mark_as_table_query']);
        add_action('pre_get_users', [$this, 'callback_meta_query'], 1);
        add_action('pre_get_users', [$this, 'callback_query_arguments'], 1);
        add_action('pre_user_query', [$this, 'callback_where'], 1);
        add_action('pre_user_query', [$this, 'callback_join'], 1);
        add_action('pre_user_query', [$this, 'callback_order_by'], 1);
    }

    /**
     * Marks the main list table query as such
     */
    public function mark_as_table_query($args): array
    {
        $args['is_list_table_query'] = 1;

        return $args;
    }

    public function callback_query_arguments(WP_User_Query $query): void
    {
        if ( ! $this->is_table_query($query)) {
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

    public function callback_meta_query(WP_User_Query $query): void
    {
        if ( ! $this->is_table_query($query)) {
            return;
        }

        $meta_query = $this->get_meta_query();

        if ( ! $meta_query) {
            return;
        }

        if (isset($query->query_vars['meta_query']) && ! empty($query->query_vars['meta_query'])) {
            $meta_query[] = $query->query_vars['meta_query'];
        }

        $query->query_vars['meta_query'] = $meta_query;
    }

    public function callback_where(WP_User_Query $query): void
    {
        if ( ! $this->is_table_query($query)) {
            return;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $query->query_where .= ' AND ' . $binding->get_where();
            }
        }
    }

    public function callback_join(WP_User_Query $query): void
    {
        if ( ! $this->is_table_query($query)) {
            return;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_join()) {
                $query->query_from .= "\n" . $binding->get_join();
            }
        }
    }

    public function callback_order_by(WP_User_Query $query): void
    {
        if ( ! $this->is_table_query($query)) {
            return;
        }

        foreach ($this->bindings as $binding) {
            if ($binding->get_order_by()) {
                $query->query_orderby = "ORDER BY " . $binding->get_order_by();
            }
            if ($binding->get_group_by()) {
                $query->query_orderby = "GROUP BY " . $binding->get_group_by() . "\n" . $query->query_orderby;
            }
        }
    }

    private function is_table_query(WP_User_Query $query): bool
    {
        return isset($query->query_vars['is_list_table_query']) && 1 === $query->query_vars['is_list_table_query'];
    }

}