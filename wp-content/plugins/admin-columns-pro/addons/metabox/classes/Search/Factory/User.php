<?php

namespace ACA\MetaBox\Search\Factory;

use AC\Meta\QueryMetaFactory;
use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Value;

final class User extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        if ($column->is_multiple()) {
            return new Search\Comparison\Table\Users(
                $default->get_operators(),
                $column->get_storage_table(),
                $column->get_meta_key(),
                (array)$column->get_field_setting('query_args'),
                Value::STRING
            );
        }

        return new Search\Comparison\Table\User(
            $default->get_operators(),
            $column->get_storage_table(),
            $column->get_meta_key(),
            (array)$column->get_field_setting('query_args')
        );
    }

    public function create_default(Column $column)
    {
        return new ACP\Search\Comparison\Meta\User(
            $column->get_meta_key(),
            (new QueryMetaFactory())->create_by_meta_column($column)
        );
    }

    public function create_cloneable(Column $column)
    {
        return false;
    }

}