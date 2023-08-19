<?php

namespace ACA\MetaBox\ListScreen;

use ACA\MetaBox\Column;
use ACP;

class PostType extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('mb-post-type');

        $this->group = 'metabox';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\PostType\Description::class,
            Column\PostType\PluralName::class,
            Column\PostType\Supports::class,
            Column\PostType\Taxonomies::class,
        ]);
    }

}