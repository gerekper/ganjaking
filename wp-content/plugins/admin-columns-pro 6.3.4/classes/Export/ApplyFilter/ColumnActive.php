<?php

namespace ACP\Export\ApplyFilter;

use AC\Column;

class ColumnActive
{

    private $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function apply_filters(bool $is_enabled): bool
    {
        return ! apply_filters('ac/export/column/disable', ! $is_enabled, $this->column);
    }

}