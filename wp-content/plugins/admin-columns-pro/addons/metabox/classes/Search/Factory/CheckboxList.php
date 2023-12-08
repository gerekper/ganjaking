<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP\Search\Comparison;

final class CheckboxList extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        /** @var Column\CheckboxList $column */
        return new Search\Comparison\Table\MultiSelect(
            $default->get_operators(),
            $column->get_storage_table(),
            $column->get_meta_key(),
            $column->get_field_options()
        );
    }

    public function create_default(Column $column)
    {
        /** @var Column\CheckboxList $column */
        return new Search\Comparison\Select($column->get_meta_key(), $column->get_field_options());
    }

    public function create_cloneable(Column $column)
    {
        return false;
    }

}