<?php

declare(strict_types=1);

namespace ACP\Sorting\Service;

use AC\Column;
use AC\Registerable;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortableFactory;
use ACP\Sorting\Settings;

class ColumnSettings implements Registerable
{

    private $model_factory;

    private $native_sortable_factory;

    public function __construct(ModelFactory $model_factory, NativeSortableFactory $native_sortable_factory)
    {
        $this->model_factory = $model_factory;
        $this->native_sortable_factory = $native_sortable_factory;
    }

    public function register(): void
    {
        add_action('ac/column/settings', [$this, 'register_column_settings']);
    }

    public function register_column_settings(Column $column): void
    {
        $has_model = $this->model_factory->create_model($column) || $this->model_factory->create_bindings($column);

        if ($has_model) {
            $column->add_setting(new Settings($column));
        }

        $native_repository = $this->native_sortable_factory->create(
            $column->get_list_screen()
        );

        if ($native_repository->find($column->get_type())) {
            $setting = new Settings($column);
            $setting->set_default('on');

            $column->add_setting($setting);
        }
    }

}