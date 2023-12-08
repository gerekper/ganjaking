<?php

namespace ACP\Sorting\Controller;

use AC\ListScreen;
use ACP\Sorting;
use ACP\Sorting\ModelFactory;

/**
 * Handles sorting based on `QueryVars` only
 * @depecated 6.3
 */
class ManageSortHandler
{

    private $list_screen;

    private $model_factory;

    public function __construct(ListScreen $list_screen, ModelFactory $model_factory)
    {
        $this->list_screen = $list_screen;
        $this->model_factory = $model_factory;
    }

    public function handle(): void
    {
        $column_name = $_GET['orderby'] ?? null;

        if ( ! $column_name) {
            return;
        }

        $list_screen = $this->list_screen;

        if ( ! $list_screen instanceof Sorting\ListScreen) {
            return;
        }

        $column = $this->list_screen->get_column_by_name($column_name);

        if ( ! $column) {
            return;
        }

        $model = $this->model_factory->create_model($column);

        if ( ! $model) {
            return;
        }

        $list_screen->sorting($model)
                    ->manage_sorting();
    }

}