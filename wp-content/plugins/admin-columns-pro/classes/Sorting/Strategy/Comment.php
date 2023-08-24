<?php

declare(strict_types=1);

namespace ACP\Sorting\Strategy;

use ACP\Sorting\Strategy;
use ACP\Sorting\Type\Order;
use WP_Comment_Query;

/**
 * Use `QueryBindings` instead of `QueryVars` for the sorting models
 * @depecated 6.3
 */
final class Comment extends Strategy
{

    public function manage_sorting(): void
    {
        add_action('pre_get_comments', [$this, 'alter_query_with_vars']);
    }

    public function get_results(): array
    {
        _deprecated_function(__METHOD__, '6.3');

        return [];
    }

    protected function get_order_from_query(WP_Comment_Query $query): Order
    {
        return Order::create_by_string($query->query_vars['order'] ?? '');
    }

    public function alter_query_with_vars(WP_Comment_Query $query): void
    {
        // check query conditions
        if ( ! $query->query_vars['orderby']) {
            return;
        }

        // run only once
        remove_action('pre_get_comments', [$this, __FUNCTION__]);

        $this->model->set_strategy($this);
        $this->model->set_order((string)$this->get_order_from_query($query));

        $vars = $this->model->get_sorting_vars();

        if ( ! $vars) {
            return;
        }

        foreach ($vars as $key => $value) {
            if (self::is_universal_id($key)) {
                $key = 'comment__in';
            }

            $query->query_vars[$key] = $value;
        }

        // pre-sorting done with an array
        $comment__in = $query->query_vars['comment__in'] ?? null;

        if ( ! empty($comment__in)) {
            $query->query_vars['orderby'] = 'comment__in';
        }
    }

}