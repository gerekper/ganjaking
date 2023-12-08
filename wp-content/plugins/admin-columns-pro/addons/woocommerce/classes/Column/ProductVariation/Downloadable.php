<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Product_Variation;

class Downloadable extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-wc-variation_downloadable')
             ->set_label(__('Downloadable', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return '_downloadable';
    }

    public function get_value($id)
    {
        $variation = new WC_Product_Variation($id);

        return ac_helper()->icon->yes_or_no($variation->get_downloadable());
    }

    public function editing()
    {
        return new Editing\ProductVariation\Downloadable();
    }

    public function search()
    {
        return new Search\ProductVariation\Downloadable();
    }

}