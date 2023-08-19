<?php

namespace ACA\WC\Service;

use AC\Groups;
use AC\Registerable;

class ColumnGroups implements Registerable
{

    public function register(): void
    {
        add_action('ac/column_groups', [$this, 'register_column_groups']);
    }

    public function register_column_groups(Groups $groups): void
    {
        $groups->add('woocommerce', __('WooCommerce', 'codepress-admin-columns'), 15);
    }

}