<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\Strategy;
use ACP\Sorting\Type\Order;
use WP_User_Query;

/**
 * Use `QueryBindings` instead of `QueryVars` for the sorting models
 * @depecated 6.3
 */
final class User extends Strategy
{

    public function manage_sorting(): void
    {
        add_action('pre_get_users', [$this, 'alter_query_with_vars']);
    }

    /**
     * @depecated 6.3
     *
     * @param array $data
     *
     * @return array
     */
    public function get_results(array $data = []): array
    {
        $args = array_merge([
            'fields' => 'ID',
        ], $data);

        return (new WP_User_Query($args))->get_results();
    }

    protected function create_order_from_query(WP_User_Query $query): Order
    {
        return Order::create_by_string($query->get('order') ?? '');
    }

    /**
     * Handles the sorting request using sorting vars
     */
    public function alter_query_with_vars(WP_User_Query $query): void
    {
        // check query conditions
        if ( ! $query->get('orderby')) {
            return;
        }

        // run only once
        remove_action('pre_get_users', [$this, __FUNCTION__]);

        $this->model->set_strategy($this);
        $this->model->set_order((string)$this->create_order_from_query($query));

        $vars = $this->model->get_sorting_vars();

        if ( ! $vars) {
            return;
        }

        foreach ($vars as $key => $value) {
            if (self::is_universal_id($key)) {
                $key = 'include';
            }

            if ('meta_query' === $key && is_array($value)) {
                $value = self::add_meta_query($value, $query->get('meta_query'));
            }

            $query->set($key, $value);
        }

        // pre-sorting done with an array
        $include = $query->get('include');

        if ( ! empty($include)) {
            $query->set('orderby', 'include');
            $query->set('order', 'ASC'); // order as offered
        }
    }

}