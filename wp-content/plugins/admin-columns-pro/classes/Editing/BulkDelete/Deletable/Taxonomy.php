<?php

namespace ACP\Editing\BulkDelete\Deletable;

use ACP\Editing\BulkDelete;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Editing\RequestHandler;

class Taxonomy implements Deletable
{

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function get_delete_request_handler(): RequestHandler
    {
        return new BulkDelete\RequestHandler\Taxonomy($this->taxonomy);
    }

    public function user_can_delete(): bool
    {
        return current_user_can('manage_categories');
    }

    public function get_query_request_handler(): RequestHandler
    {
        return new RequestHandler\Query\Taxonomy();
    }

}