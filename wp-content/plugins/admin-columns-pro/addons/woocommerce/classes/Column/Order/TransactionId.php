<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC;
use ACP;

class TransactionId extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                                 ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_transaction_id')
             ->set_label(__('Transaction ID', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $transaction_id = $order
            ? $order->get_transaction_id()
            : false;

        return $transaction_id ?: $this->get_empty_char();
    }

    public function search()
    {
        return new WC\Search\Order\TransactionId();
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

    public function sorting()
    {
        return new WC\Sorting\Order\OrderData('transaction_id');
    }

}