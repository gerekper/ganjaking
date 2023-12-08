<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA;
use ACA\WC\Search;
use ACP;

class Shipping extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-order_shipping')
             ->set_label(__('Shipping Costs', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $price = $this->get_raw_value($id);

        if ( ! $price) {
            return $this->get_empty_char();
        }

        return wc_price(
            $this->get_raw_value($id),
            [
                'currency' => wc_get_order($id)->get_currency(),
            ]
        );
    }

    public function get_raw_value($id)
    {
        return wc_get_order($id)->get_shipping_total();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Decimal('_order_shipping');
    }

}