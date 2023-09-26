<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;
use ACP\Sorting\Type\Order;
use ACP\TermQueryInformation;
use WP_Term_Query;

/**
 * Use `QueryBindings` instead of `QueryVars` for the sorting models
 * @depecated 6.3
 */
class Taxonomy extends Strategy
{

    use TermResultsTrait;

    public function __construct(AbstractModel $model, string $taxonomy)
    {
        parent::__construct($model);

        $this->taxonomy = $taxonomy;
    }

    public function manage_sorting(): void
    {
        add_action('pre_get_terms', [$this, 'alter_query_vars']);
    }

    protected function get_order(WP_Term_Query $query): Order
    {
        return Order::create_by_string($query->query_vars['order'] ?? '');
    }

    private function is_main_query(WP_Term_Query $query): bool
    {
        if ( ! isset($query->query_vars['orderby']) || ! TermQueryInformation::is_main_query($query)) {
            return false;
        }

        $taxonomies = $query->query_vars['taxonomy'] ?? [];

        return $taxonomies && in_array($this->taxonomy, $taxonomies, true);
    }

    public function alter_query_vars(WP_Term_Query $query): void
    {
        if ( ! $this->is_main_query($query)) {
            return;
        }

        $this->model->set_strategy($this);
        $this->model->set_order((string)$this->get_order($query));

        $vars = $this->model->get_sorting_vars();

        if ( ! $vars) {
            return;
        }

        foreach ($vars as $key => $value) {
            if (self::is_universal_id($key)) {
                $key = 'include';
            }

            $query->query_vars[$key] = $value;
        }

        $include = $query->query_vars['include'] ?? null;

        if ( ! empty($include)) {
            $query->query_vars['order'] = 'desc';
            $query->query_vars['orderby'] = 'include';
        }
    }

}