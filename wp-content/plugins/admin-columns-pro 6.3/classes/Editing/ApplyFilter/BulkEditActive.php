<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class BulkEditActive
{

    private $column;

    public function __construct(AC\Column $column)
    {
        $this->column = $column;
    }

    public function apply_filters(bool $is_active): bool
    {
        /**
         * @deprecated 5.7
         */
        $is_active = (bool)apply_filters('acp/editing/bulk-edit-active', $is_active, $this->column);

        return (bool)apply_filters(
            'acp/editing/bulk/is_active',
            $is_active,
            $this->column,
            $this->column->get_list_screen()
        );
    }

}