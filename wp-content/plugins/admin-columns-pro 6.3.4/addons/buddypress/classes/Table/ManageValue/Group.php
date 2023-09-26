<?php

declare(strict_types=1);

namespace ACA\BP\Table\ManageValue;

use AC\Table\ManageValue;

class Group extends ManageValue
{

    public function register(): void
    {
        add_filter('bp_groups_admin_get_group_custom_column', [$this, 'render_value'], 100, 3);
    }

    public function render_value($value, $column_name, $group): ?string
    {
        return $this->render_cell((string)$column_name, (int)$group['id'], (string)$value);
    }

}