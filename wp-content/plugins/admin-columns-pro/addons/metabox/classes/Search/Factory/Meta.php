<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;

final class Meta extends Search\Factory implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, ACP\Search\Comparison $default)
    {
        switch (true) {
            case $default instanceof ACP\Search\Comparison\Meta\Media:
                return new Search\Comparison\Table\Media(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key(),
                    (array)$this->get_mimetype_by_comparison($default),
                    $default->get_value_type()
                );
            default:
                return new Search\Comparison\Table\TableStorage(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key(),
                    $default->get_value_type()
                );
        }
    }

    private function get_mimetype_by_comparison(ACP\Search\Comparison $comparison): string
    {
        switch (true) {
            case $comparison instanceof ACP\Search\Comparison\Meta\Image:
                return 'image';
            default:
                return '';
        }
    }

    public function create_default(Column $column)
    {
        switch (true) {
            case $column instanceof Column\Number:
                return new ACP\Search\Comparison\Meta\Number($column->get_meta_key());
            case $column instanceof Column\Checkbox:
                return new ACP\Search\Comparison\Meta\Checkmark($column->get_meta_key());
            case $column instanceof Column\File:
                return new ACP\Search\Comparison\Meta\Media(
                    $column->get_meta_key(),
                    $this->get_meta_query($column)
                );
            case $column instanceof Column\Image:
            case $column instanceof Column\SingleImage:
                return new ACP\Search\Comparison\Meta\Image(
                    $column->get_meta_key(),
                    $this->get_meta_query($column)
                );
            default:
                return new ACP\Search\Comparison\Meta\Text($column->get_meta_key());
        }
    }

    public function create_cloneable(Column $column)
    {
        return new ACP\Search\Comparison\Meta\Serialized($column->get_meta_key());
    }

}