<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.2
 */
class Featured extends AC\Column

    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Editing\Editable
{

    public function __construct()
    {
        $this->set_type('featured')
             ->set_original(true);
    }

    public function get_value($id)
    {
        return null;
    }

    public function get_raw_value($id)
    {
        return wc_get_product($id)->is_featured();
    }

    public function sorting()
    {
        return new Sorting\Product\Featured();
    }

    public function editing()
    {
        return new Editing\Product\Featured();
    }

    public function search()
    {
        return new Search\Product\Featured();
    }

}