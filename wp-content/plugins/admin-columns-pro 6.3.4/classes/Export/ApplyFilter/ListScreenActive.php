<?php

namespace ACP\Export\ApplyFilter;

use AC\ListScreen;

class ListScreenActive
{

    private $list_screen;

    public function __construct(ListScreen $list_screen)
    {
        $this->list_screen = $list_screen;
    }

    public function apply_filters(bool $is_active): bool
    {
        return (bool)apply_filters('acp/export/is_active', $is_active, $this->list_screen);
    }

}