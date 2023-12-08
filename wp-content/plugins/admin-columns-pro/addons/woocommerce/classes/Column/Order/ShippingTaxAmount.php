<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class ShippingTaxAmount extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                                     ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_shipping_tax')
             ->set_label(__('Shipping Tax Amount', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $price = $this->get_raw_value($id);

        return $price
            ? wc_price($price)
            : $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_shipping_tax() : 0;
    }

    public function search()
    {
        return new Search\Order\OperationalDataPrice('shipping_tax_amount');
    }

    public function sorting()
    {
        return new Sorting\Order\OperationalData(
            'shipping_tax_amount',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

}