<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class User extends TableRows
{

    public function register(): void
    {
        add_action('users_list_table_query_args', [$this, 'handle_request']);
    }

    public function handle_request(): void
    {
        remove_action('users_list_table_query_args', [$this, __FUNCTION__]);

        parent::handle_request();
    }

}