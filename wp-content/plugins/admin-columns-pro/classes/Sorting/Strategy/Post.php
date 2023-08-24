<?php

declare(strict_types=1);

namespace ACP\Sorting\Strategy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;
use ACP\Sorting\Type\Order;
use WP_Query;

/**
 * Use `QueryBindings` instead of `QueryVars` for the sorting models
 * @depecated 6.3
 */
class Post extends Strategy
{

    use PostResultsTrait;

    private $post_type;

    public function __construct(AbstractModel $model, string $post_type)
    {
        parent::__construct($model);

        $this->post_type = $post_type;
    }

    public function manage_sorting(): void
    {
        add_action('pre_get_posts', [$this, 'alter_query_with_vars']);
    }

    private function is_main_query(WP_Query $query): bool
    {
        return $query->is_main_query() &&
               $query->get('orderby') &&
               $query->get('post_type') === $this->post_type;
    }

    protected function get_order(WP_Query $query): Order
    {
        return Order::create_by_string($query->query['order'] ?? '');
    }

    protected function get_pagination_per_page(): int
    {
        return (int)get_user_option('edit_' . $this->post_type . '_per_page');
    }

    /**
     * Handle the sorting request on the post-type listing screens
     */
    public function alter_query_with_vars(WP_Query $query): void
    {
        if ( ! $this->is_main_query($query)) {
            return;
        }

        $this->model->set_order((string)$this->get_order($query));
        $this->model->set_strategy($this);

        $vars = $this->model->get_sorting_vars();

        if ( ! $vars) {
            return;
        }

        foreach ($vars as $key => $value) {
            if (self::is_universal_id($key)) {
                $key = 'post__in';

                $query->set('orderby', $key);
            }

            if ('meta_query' === $key) {
                $value = self::add_meta_query($value, $query->get($key));
            }

            $query->set($key, $value);
        }
    }

}