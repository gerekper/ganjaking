<?php

namespace ACA\WC\Export\Order;

use ACP;

class Status implements ACP\Export\Service
{

    public function get_value($id)
    {
        $statuses = wc_get_order_statuses();
        $status = 'wc-' . wc_get_order($id)->get_status();

        return array_key_exists($status, $statuses)
            ? $statuses[$status]
            : $status;
    }

}