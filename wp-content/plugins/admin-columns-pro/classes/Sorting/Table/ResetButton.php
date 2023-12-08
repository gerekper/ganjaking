<?php

namespace ACP\Sorting\Table;

use AC\ColumnRepository;
use AC\Table;
use ACP\Sorting;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\Type\SortType;

class ResetButton
{

    private $column_repository;

    private $setting_sort_default;

    private $default_sort_filter;

    public function __construct(
        ColumnRepository $column_repository,
        Sorting\Settings\ListScreen\PreferredSort $setting_sort_default,
        ApplyFilter\DefaultSort $default_sort_filter
    ) {
        $this->column_repository = $column_repository;
        $this->setting_sort_default = $setting_sort_default;
        $this->default_sort_filter = $default_sort_filter;
    }

    private function is_default(SortType $request_sort_type): bool
    {
        $sort_type = $this->default_sort_filter->apply_filters(
            $this->setting_sort_default->get()
        );

        if ( ! $sort_type) {
            return false;
        }

        return $sort_type->equals($request_sort_type);
    }

    public function get(SortType $sort_type): ?Table\Button
    {
        if ($this->is_default($sort_type)) {
            return null;
        }

        if ( ! $sort_type->get_order_by()) {
            return null;
        }

        $button = new Table\Button('edit-columns');
        $button->set_url('#')
               ->set_text(__('Reset Sorting', 'codepress-admin-columns'))
               ->set_attribute('class', 'ac-table-button reset-sorting');

        $column = $this->column_repository->find($sort_type->get_order_by());

        if ($column) {
            $label = strip_tags($column->get_custom_label());

            if (empty($label)) {
                $label = $column->get_label();
            }

            $button->set_label(trim(__('Sorted by ', 'codepress-admin-columns')) . ' ' . $label);
        }

        return $button;
    }

}