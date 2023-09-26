<?php

namespace ACA\MetaBox\Sorting\Factory;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\Disabled;

final class AdvancedTaxonomy extends Sorting\Factory implements MetaBox\Sorting\CloneableFactory,
                                                                Sorting\TableStorageFactory
{

    public function create_table_storage(Column $column)
    {
        return (new TableStorageFactory())->create_table_storage($column);
    }

    protected function create_default(Column $column): AbstractModel
    {
        return (new ACP\Sorting\Model\MetaFormatFactory())->create(
            $column->get_meta_type(),
            $column->get_meta_key(),
            new Sorting\FormatValue\Taxonomy(),
            null,
            [
                'post_type' => $column->get_post_type(),
                'taxonomy'  => $column->get_taxonomy(),
            ]
        );
    }

    public function create_cloneable(Column $column): Disabled
    {
        return new Disabled();
    }

}