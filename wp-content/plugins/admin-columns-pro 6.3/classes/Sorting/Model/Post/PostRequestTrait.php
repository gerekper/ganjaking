<?php

namespace ACP\Sorting\Model\Post;

trait PostRequestTrait
{

    public function get_var_post_type(): string
    {
        global $current_screen;

        return $current_screen->post_type;
    }

    public function get_var_post_status(): ?string
    {
        $status = $_GET['post_status'] ?? null;

        return in_array($status, get_post_stati(), true)
            ? $status
            : null;
    }

    public function get_var_post_author(): ?int
    {
        $author = $_GET['author'] ?? '';

        return (int)$author;
    }

}