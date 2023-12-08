<?php

declare(strict_types=1);

namespace ACA\GravityForms;

use AC;
use GF_Entry_List_Table;
use GFAPI;

class ListTable implements AC\ListTable
{

    private $table;

    public function __construct(GF_Entry_List_Table $table)
    {
        $this->table = $table;
    }

    public function get_column_value(string $column, $id): string
    {
        ob_start();
        $this->table->column_default(GFAPI::get_entry($id), $column);

        return ob_get_clean();
    }

    public function get_total_items(): int
    {
        return $this->table->get_pagination_arg('total_items');
    }

    public function render_row($id): string
    {
        ob_start();
        $this->table->single_row(GFAPI::get_entry($id));

        return ob_get_clean();
    }

}