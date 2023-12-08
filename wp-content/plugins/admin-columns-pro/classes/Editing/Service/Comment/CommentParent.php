<?php

namespace ACP\Editing\Service\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;
use ACP\Helper\Select\Comment\PaginatedFactory;

class CommentParent extends Service\BasicStorage implements PaginatedOptions
{

    public function __construct()
    {
        parent::__construct(new Storage\Comment\Field('comment_parent'));
    }

    public function get_view(string $context): ?View
    {
        $view = new View\AjaxSelect();
        $view->set_multiple(false);

        return $view;
    }

    public function get_paginated_options(string $search, int $page, $id = null): Paginated
    {
        return (new PaginatedFactory())->create([
            'search' => $search,
            'paged'  => $page,
        ]);
    }

}