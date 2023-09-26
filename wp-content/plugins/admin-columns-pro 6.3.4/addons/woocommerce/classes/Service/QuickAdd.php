<?php

namespace ACA\WC\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\WC\ListScreen\ShopOrder;

class QuickAdd implements Registerable
{

    public function register(): void
    {
        add_filter('acp/quick_add/enable', [$this, 'disable_quick_add'], 10, 2);
    }

    public function disable_quick_add(bool $enabled, ListScreen $list_screen): bool
    {
        if ($list_screen instanceof ShopOrder) {
            return false;
        }

        return $enabled;
    }

}