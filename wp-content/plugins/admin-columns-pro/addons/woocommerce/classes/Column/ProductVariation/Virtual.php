<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Product_Variation;

class Virtual extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-wc-variation_virtual')
             ->set_label(__('Virtual', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return '_virtual';
    }

    public function get_value($id)
    {
        $variation = new WC_Product_Variation($id);

        return ac_helper()->icon->yes_or_no($variation->get_virtual());
    }

    public function editing()
    {
        return new Editing\ProductVariation\Virtual();
    }

    public function search()
    {
        return new Search\ProductVariation\Virtual();
    }

}