<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACP;

class Price extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('price')
             ->set_original(true);
    }

    public function get_value($id)
    {
        return null;
    }

    public function get_meta_key()
    {
        return '_price';
    }

    public function editing()
    {
        return new Editing\Product\Price();
    }

    public function search()
    {
        $include_tax = 'yes' === get_option('woocommerce_prices_include_tax');
        $display_tax = 'incl' === get_option('woocommerce_tax_display_shop');

        if ($include_tax && ! $display_tax) {
            return null;
        }
        if ( ! $include_tax && $display_tax) {
            return null;
        }

        return new ACP\Search\Comparison\Meta\Decimal($this->get_meta_key());
    }

}