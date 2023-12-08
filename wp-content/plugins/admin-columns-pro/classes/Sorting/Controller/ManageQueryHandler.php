<?php

declare(strict_types=1);

namespace ACP\Sorting\Controller;

use AC\ListScreen;
use ACP\QueryFactory;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\Type\Order;

class ManageQueryHandler
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

        $column = $this->list_screen->get_column_by_name($column_name);

        if ( ! $column) {
            return;
        }

        $model = $this->model_factory->create_bindings($column);

        if ( ! $model) {
            return;
        }

        $order = Order::create_by_string($_GET['order'] ?? '');

        QueryFactory::create(
            $this->list_screen->get_query_type(),
            [
                $model->create_query_bindings($order),
            ]
        )->register();
    }
}