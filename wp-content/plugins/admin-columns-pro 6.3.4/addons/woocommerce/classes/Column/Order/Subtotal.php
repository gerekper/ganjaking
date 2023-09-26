<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Sorting\Order\Stats;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Sorting\Type\DataType;

class Subtotal extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable,
                                            ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_subtotal')
             ->set_label(__('Subtotal', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        return $order
            ? $order->get_subtotal_to_display()
            : false;
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_subtotal() : false;
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    public function sorting()
    {
        return new Stats('net_total', new DataType(DataType::NUMERIC));
    }

}