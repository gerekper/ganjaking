<?php

namespace ACP\Editing\BulkDelete\Deletable;

use ACP\Editing\BulkDelete;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Editing\RequestHandler;
use WP_Post_Type;

class Post implements Deletable
{

    protected $post_type;

    public function __construct(WP_Post_Type $post_type)
    {
        $this->post_type = $post_type;
    }

    public function user_can_delete(): bool
    {
        return current_user_can($this->post_type->cap->delete_posts);
    }

    public function get_delete_request_handler(): RequestHandler
    {
        return new BulkDelete\RequestHandler\Post($this->post_type);
    }

    public function get_query_request_handler(): RequestHandler
    {
        return new RequestHandler\Query\Post();
    }

}