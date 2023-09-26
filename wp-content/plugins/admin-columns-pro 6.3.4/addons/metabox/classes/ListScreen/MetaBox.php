<?php

namespace ACA\MetaBox\ListScreen;

use ACA\MetaBox\Column;
use ACP;

class MetaBox extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('meta-box');

        $this->group = 'metabox';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\MetaBox\NumberOfFields::class,
            Column\MetaBox\Id::class,
            Column\MetaBox\Fields::class,
            Column\MetaBox\CustomTable::class,
            Column\MetaBox\Position::class,
        ]);
    }

}