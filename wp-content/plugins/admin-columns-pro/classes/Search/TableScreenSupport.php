<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\ListScreen;

class TableScreenSupport
{

    public static function is_searchable(ListScreen $list_screen): bool
    {
        return null !== TableScreenFactory::get_table_screen_reference($list_screen);
    }

}