<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;
use ACP\Search\Comparison;

final class Autocomplete extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        switch (true) {
            case $default instanceof ACP\Search\Comparison\Meta\Text:
                return new Search\Comparison\Table\TableStorage(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key(),
                    $default->get_value_type()
                );

            case $default instanceof Search\Comparison\MultiSelect:
            case $default instanceof Search\Comparison\Select:
                return new Search\Comparison\Table\MultiSelect(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key(),
                    $this->get_field_options_from_column($column)
                );

            default:
                return false;
        }
    }

    private function get_field_options_from_column(Column $column): array
    {
        $options = $column->get_field_setting('options');

        return $options
            ? (array)$options
            : [];
    }

    public function create_default(Column $column)
    {
        /** @var Column\Autocomplete $column */
        if ($column->is_ajax()) {
            return new ACP\Search\Comparison\Meta\Text($column->get_meta_key());
        }

        return new Search\Comparison\Select(
            $column->get_meta_key(),
            $this->get_field_options_from_column($column)
        );
    }

    public function create_cloneable(Column $column)
    {
        return new Search\Comparison\MultiSelect(
            $column->get_meta_key(),
            $this->get_field_options_from_column($column)
        );
    }

}