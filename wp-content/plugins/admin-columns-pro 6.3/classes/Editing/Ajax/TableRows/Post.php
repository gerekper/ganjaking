<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class Post extends TableRows
{

    public function register(): void
    {
        add_action('edit_posts_per_page', [$this, 'handle_request']);
    }

    public function handle_request(): void
    {
        remove_action('edit_posts_per_page', [$this, __FUNCTION__]);

        parent::handle_request();
    }

}