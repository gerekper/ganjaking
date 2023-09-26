<?php

namespace ACP\Sorting\Table\Filter;

use AC\Column;
use AC\ColumnRepository;
use ACP\Sorting;

class DisabledOriginalColumns implements ColumnRepository\Filter
{

    public function filter(array $columns): array
    {
        return array_filter($columns, [$this, 'is_disabled']);
    }

    private function is_disabled(Column $column): bool
    {
        if ( ! $column->is_original()) {
            return false;
        }

        $setting = $column->get_setting('sort');

        return $setting instanceof Sorting\Settings && ! $setting->is_active();
    }
}