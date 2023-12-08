<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACA\WC\Sorting\Order\OrderData;
use ACP;
use ACP\Sorting\Type\DataType;

class IsCustomer extends AC\Column implements ACP\Search\Searchable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_is_customer')
             ->set_label(__('Is Customer', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $customer_id = $this->get_customer_id($id);

        return $customer_id !== 0
            ? ac_helper()->icon->yes(get_userdata($customer_id)->display_name)
            : ac_helper()->icon->no(__('Guest', 'woocommerce'));
    }

    public function get_raw_value($id)
    {
        return $this->get_customer_id((int)$id) !== 0;
    }

    private function get_customer_id(int $id): int
    {
        $order = wc_get_order($id);

        return $order
            ? $order->get_customer_id()
            : 0;
    }

    public function search()
    {
        return new Search\Order\IsCustomer();
    }

    public function sorting()
    {
        return new OrderData(
            Orders::CUSTOMER_ID,
            new DataType(DataType::NUMERIC)
        );
    }

}