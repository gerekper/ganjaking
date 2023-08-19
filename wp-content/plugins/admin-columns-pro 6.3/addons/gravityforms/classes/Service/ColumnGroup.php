<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC;
use AC\Registerable;

class ColumnGroup implements Registerable
{

    public function register(): void
    {
        add_action('ac/column_groups', [$this, 'register_column_group']);
    }

    public function register_column_group(AC\Groups $groups): void
    {
        $groups->add('gravity_forms', __('Gravity Forms', 'codepress-admin-columns'), 14);
    }

}