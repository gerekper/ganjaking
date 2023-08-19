<?php

namespace ACA\ACF\ListScreen;

use ACA\ACF;
use ACP;
use ACP\Column;

class FieldGroup extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('acf-field-group');

        $this->group = 'acf';
    }

    protected function register_column_types(): void
    {
        $this->register_column_types_from_list([
            Column\CustomField::class,
            Column\Actions::class,
            Column\Post\Author::class,
            Column\Post\Content::class,
            Column\Post\DatePublished::class,
            Column\Post\ID::class,
            Column\Post\Modified::class,
            Column\Post\Status::class,
            Column\Post\Title::class,
            Column\Post\TitleRaw::class,
            ACF\Column\FieldGroup\Location::class,
        ]);
    }

}