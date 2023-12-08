<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;
use ACP\Search\Comparison;

final class Post extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        return new Search\Comparison\Table\Post(
            $default->get_operators(),
            $column->get_storage_table(),
            $column->get_meta_key(),
            (array)$column->get_field_setting('post_type'),
            (array)$column->get_field_setting('query_args')
        );
    }

    public function create_default(Column $column)
    {
        return new ACP\Search\Comparison\Meta\Post(
            $column->get_meta_key(),
            (array)$column->get_field_setting('post_type')
        );
    }

    public function create_cloneable(Column $column)
    {
        return false;
    }

}