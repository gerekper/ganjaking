<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use WP_Comment_Query;

class Comment extends Query
{

    public function register(): void
    {
        add_action('pre_get_comments', [$this, 'callback_meta_query'], 1);
        add_action('pre_get_comments', [$this, 'callback_parent'], 1);
        add_action('pre_get_comments', [$this, 'callback_query_arguments'], 1);
        add_filter('comments_clauses', [$this, 'callback_clauses'], 20);
    }

    public function callback_meta_query(WP_Comment_Query $query): void
    {
        $meta_query = $this->get_meta_query();

        if ( ! $meta_query) {
            return;
        }

        if ( ! empty($query->query_vars['meta_query'])) {
            $meta_query[] = $query->query_vars['meta_query'];
        }

        $query->query_vars['meta_query'] = $meta_query;
    }

    public function callback_parent(WP_Comment_Query $query): void
    {
        foreach ($this->bindings as $binding) {
            if ($binding instanceof Query\Bindings\Comment && $binding->get_parent()) {
                $query->query_vars['parent'] = $binding->get_parent();
            }
        }
    }

    public function callback_query_arguments(WP_Comment_Query $query): void
    {
        foreach ($this->bindings as $binding) {
            if ($binding instanceof Query\Bindings\QueryArguments) {
                foreach ($binding->get_query_arguments() as $query_var => $value) {
                    $query->query_vars[$query_var] = $value;
                }
            }
        }
    }

    public function callback_clauses(array $comments_clauses): array
    {
        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $comments_clauses['where'] .= ' AND ' . $binding->get_where();
            }
            if ($binding->get_join()) {
                $comments_clauses['join'] .= "\n" . $binding->get_join();
            }
            if ($binding->get_order_by()) {
                $comments_clauses['orderby'] = $binding->get_order_by();
            }
            if ($binding->get_group_by()) {
                $comments_clauses['groupby'] = $binding->get_group_by();
            }
        }

        return $comments_clauses;
    }

}