<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class Ip extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_customer_ip')
             ->set_label(__('Customer IP address', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_customer_ip_address() : $this->get_empty_char();
    }

    public function search()
    {
        return new Search\Order\CustomerIp();
    }

    public function sorting()
    {
        return new Sorting\Order\OrderData(Orders::IP_ADDRESSS);
    }

}