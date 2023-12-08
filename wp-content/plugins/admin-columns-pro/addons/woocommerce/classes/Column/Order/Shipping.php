<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Sorting\Type\DataType;

class Shipping extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                            ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_shipping')
             ->set_label(__('Shipping', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $price = $order ? $order->get_shipping_total() : 0;

        return $price ? wc_price($price, ['currency', $order->get_currency()]) : $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_shipping_total() : false;
    }

    public function search()
    {
        return new WC\Search\Order\ShippingTotal();
    }

    public function sorting()
    {
        return new WC\Sorting\Order\OperationalData(
            'shipping_total_amount',
            new DataType(DataType::NUMERIC)
        );
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

}