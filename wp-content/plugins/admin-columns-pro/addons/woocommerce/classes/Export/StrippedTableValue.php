<?php

namespace ACA\WC\Export;

use AC;
use ACP;

class StrippedTableValue implements ACP\Export\Service
{

    protected $column;

    public function __construct(AC\Column $column)
    {
        $this->column = $column;
    }

    public function get_value($id)
    {
        $list_screen = $this->column->get_list_screen();

        return $this->column->get_list_screen() instanceof AC\ListScreen\ListTable
            ? strip_tags($list_screen->list_table()->get_column_value($this->column->get_name(), $id))
            : '';
    }

}