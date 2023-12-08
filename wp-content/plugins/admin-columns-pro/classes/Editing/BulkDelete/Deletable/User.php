<?php

namespace ACP\Editing\BulkDelete\Deletable;

use ACP\Editing;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Editing\RequestHandler;

class User implements Deletable
{

    public function get_delete_request_handler(): RequestHandler
    {
        return new Editing\BulkDelete\RequestHandler\User();
    }

    public function user_can_delete(): bool
    {
        return current_user_can('delete_users');
    }

    public function get_query_request_handler(): RequestHandler
    {
        return new Editing\RequestHandler\Query\User();
    }

}