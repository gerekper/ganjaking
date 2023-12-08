<?php

namespace ACA\MetaBox\Search\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Search;
use ACP\Search\Comparison;

final class Taxonomy extends Search\Factory implements Search\TableStorageFactory
{

    public function create_table_storage(Column $column, Comparison $default)
    {
        if ($default instanceof Search\Comparison\Taxonomy) {
            return $default;
        }

        return $this->create_disabled($column);
    }

    public function create_default(Column $column)
    {
        if ($column instanceof Column\AdvancedTaxonomy) {
            return new Search\Comparison\TaxonomyAdvanced($column->get_taxonomy(), $column->get_meta_key());
        }

        return new Search\Comparison\Taxonomy((array)$column->get_taxonomy());
    }

}