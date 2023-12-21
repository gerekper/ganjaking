<?php

namespace ACP;

use AC\Column;
use AC\Registerable;
use ACP\Settings\Column\Label;

class IconPicker implements Registerable
{

    public function register(): void
    {
        add_action('ac/column/settings', [$this, 'register_column_settings']);
    }

    /**
     * Replace the default label setting with a pro version that includes an icon picker
     */
    public function register_column_settings(Column $column): void
    {
        // We overwrite the default label setting with a pro version
        $column->add_setting(new Label($column));
    }

}