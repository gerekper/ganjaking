<?php

namespace ACA\WC\Export\OrderSubscription;

use ACP;

class Status implements ACP\Export\Service
{

    public function get_value($id)
    {
        $statuses = wcs_get_subscription_statuses();
        $status = 'wc-' . wcs_get_subscription($id)->get_status();

        return array_key_exists($status, $statuses)
            ? $statuses[$status]
            : $status;
    }

}