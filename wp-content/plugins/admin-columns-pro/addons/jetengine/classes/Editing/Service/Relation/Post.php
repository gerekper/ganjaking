<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Editing;
use ACP;
use ACP\Helper\Select\Post\PaginatedFactory;

class Post extends Editing\Service\Relationship
{

    private $related_post_type;

    public function __construct(ACP\Editing\Storage $storage, bool $multiple, string $related_post_type)
    {
        $this->related_post_type = $related_post_type;

        parent::__construct($storage, $multiple);
    }

    public function get_value($id)
    {
        $value = [];
        $post_ids = parent::get_value($id);

        foreach ($post_ids as $post_id) {
            $value[$post_id] = get_the_title($post_id);
        }

        return $value;
    }

    public function get_paginated_options(string $search, int $page, int $id = null): Paginated
    {
        return (new PaginatedFactory())->create([
            'paged'     => $page,
            's'         => $search,
            'post_type' => $this->related_post_type,
        ]);
    }

}