<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;
use WP_Query;

final class Media extends TableRows
{

    public function register(): void
    {
        add_action('pre_get_posts', [$this, 'pre_handle_request']);
    }

    public function pre_handle_request(WP_Query $query): void
    {
        if ( ! $query->is_main_query()) {
            return;
        }

        $this->handle_request();
        remove_action('pre_get_posts', [$this, __FUNCTION__]);
    }

}