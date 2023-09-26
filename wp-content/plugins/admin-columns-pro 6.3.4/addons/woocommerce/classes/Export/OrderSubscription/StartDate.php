<?php

namespace ACA\WC\Export\OrderSubscription;

use ACP;

class StartDate implements ACP\Export\Service
{

    public function get_value($id)
    {
        return wcs_get_subscription($id)->get_date('start_date');
    }

}