<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Date extends Service\DateTime implements Service\Editability
{

    public function __construct()
    {
        parent::__construct(new View\DateTime(), new Storage\Post\Date());
    }

    public function is_editable(int $id): bool
    {
        return ! $this->is_unsupported_status(get_post($id)->post_status);
    }

    public function get_not_editable_reason(int $id): string
    {
        $post = get_post($id);

        return sprintf(
            __('Date can not be updated for %s with %s status.', 'codepress-admin-columns'),
            get_post_type_object($post->post_type)->labels->singular_name,
            $post->post_status
        );
    }

    protected function is_unsupported_status(string $status): bool
    {
        return in_array($status, ['draft', 'inherit'], true);
    }

}