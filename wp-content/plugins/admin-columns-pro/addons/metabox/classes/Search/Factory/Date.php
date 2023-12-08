<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP;
use ACP\Search\Comparison;

final class Date extends Search\Factory
    implements Search\CloneableFactory, Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        switch (true) {
            case $default instanceof ACP\Search\Comparison\Meta\DateTime\Timestamp:
                return new Search\Comparison\Table\Timestamp(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key()
                );
            default:
                return new Search\Comparison\Table\DateIso(
                    $default->get_operators(),
                    $column->get_storage_table(),
                    $column->get_meta_key()
                );
        }
    }

    public function create_default(Column $column)
    {
        /** @var Column\Date $column */
        switch ($column->get_saved_format()) {
            case 'U':
                return new ACP\Search\Comparison\Meta\DateTime\Timestamp(
                    $column->get_meta_key(),
                    $this->get_meta_query($column)
                );
            case 'Y-m-d':
                return new ACP\Search\Comparison\Meta\Date(
                    $column->get_meta_key(),
                    $this->get_meta_query($column)
                );
            case 'Y-m-d H:i:s':
                return new ACP\Search\Comparison\Meta\DateTime\ISO(
                    $column->get_meta_key(),
                    $this->get_meta_query($column)
                );
            case 'Y-m-d H:i':
            default:
                return false;
        }
    }

    public function create_cloneable(Column $column)
    {
        return false;
    }

}