<?php

namespace ACA\WC\Export\Order;

use ACP;

class OrderTitle implements ACP\Export\Service
{

    public function get_value($id)
    {
        $order = wc_get_order($id);

        return sprintf('#%s %s', $order->get_id(), $order->get_formatted_billing_full_name());
    }

}