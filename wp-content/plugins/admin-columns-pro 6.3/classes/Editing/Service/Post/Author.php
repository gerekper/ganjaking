<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use ACP\Editing\Storage;
use ACP\Editing\View\AjaxSelect;

class Author extends Service\User implements Editability
{

    public function __construct()
    {
        parent::__construct(
            new AjaxSelect(),
            new Storage\Post\Field('post_author')
        );
    }

    public function is_editable(int $id): bool
    {
        return ! current_user_can('author') || current_user_can('administrator');
    }

    public function get_not_editable_reason(int $id): string
    {
        return __('You can not change the author.', 'codepress-admin-columns');
    }

}