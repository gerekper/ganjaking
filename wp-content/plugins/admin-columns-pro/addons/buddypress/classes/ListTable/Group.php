<?php

declare(strict_types=1);

namespace ACA\BP\ListTable;

use AC\ListTable;
use BP_Groups_List_Table;

class Group implements ListTable
{

    use ListTable\WpListTableTrait;

    public function __construct(BP_Groups_List_Table $table)
    {
        $this->table = $table;
    }

    public function get_column_value(string $column, $id): string
    {
        return (string)apply_filters('bp_groups_admin_get_group_custom_column', '', $column, $this->get_group($id));
    }

    public function render_row($id): string
    {
        ob_start();

        $this->table->single_row($this->get_group($id));

        return ob_get_clean();
    }

    private function get_group(int $id): array
    {
        return (array)groups_get_group($id);
    }

}