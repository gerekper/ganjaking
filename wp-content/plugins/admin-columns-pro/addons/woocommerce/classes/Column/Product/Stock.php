<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;
use ACP\Sorting\Type\DataType;

class Stock extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('is_in_stock')
             ->set_original(true);
    }

    public function editing()
    {
        return new Editing\Product\Stock();
    }

    public function export()
    {
        return new Export\Product\Stock();
    }

    public function search()
    {
        return new Search\Product\Stock();
    }

    public function sorting()
    {
        // TODO use `wc_product_meta_lookup` table
        return new ACP\Sorting\Model\Post\Meta('_stock', new DataType(DataType::NUMERIC));
    }

}