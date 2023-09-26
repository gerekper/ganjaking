<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC;
use ACP;

class PaymentMethod extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                                 ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_payment_method')
             ->set_label(__('Payment Method', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $title = strip_tags($order->get_payment_method_title()) ?: $order->get_payment_method();

        return $title ?: $this->get_empty_char();
    }

    public function search()
    {
        return new WC\Search\Order\PaymentMethod();
    }

    public function sorting()
    {
        return new WC\Sorting\Order\OrderData('payment_method_title');
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

}