<?php

namespace ACA\WC\Service;

use AC;
use AC\Registerable;
use ACA\WC;
use ACA\WC\ListScreen;

class TableRows implements Registerable
{

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'register_table_rows']);
    }

    public function register_table_rows(AC\ListScreen $list_screen)
    {
        if ( ! $list_screen instanceof ListScreen\Order) {
            return;
        }

        $table_rows = new WC\Editing\TableRows\Order(new AC\Request(), $list_screen);

        if ($table_rows->is_request()) {
            $table_rows->register();
        }
    }

}