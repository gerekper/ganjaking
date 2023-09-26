<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use WC_Order;

class PaidAmount extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-order_paid_amount')
             ->set_label(__('Paid Amount', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $price = $order ? $this->get_paid_amount($order) : 0;

        return $price > 0
            ? wc_price($price, ['currency' => $order->get_currency()])
            : $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $this->get_paid_amount($order) : 0;
    }

    private function get_paid_amount(WC_Order $order): float
    {
        return $order->is_paid()
            ? $order->get_total() - $order->get_total_refunded()
            : 0;
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