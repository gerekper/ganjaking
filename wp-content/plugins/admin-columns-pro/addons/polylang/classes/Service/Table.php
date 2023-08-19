<?php

declare(strict_types=1);

namespace ACA\Polylang\Service;

use AC;
use AC\Registerable;

class Table implements Registerable
{

    public function register(): void
    {
        add_action('ac/table/list_screen', static function (AC\ListScreen $list_screen) {
            (new ColumnReplacement($list_screen))->register();
        });
    }

}