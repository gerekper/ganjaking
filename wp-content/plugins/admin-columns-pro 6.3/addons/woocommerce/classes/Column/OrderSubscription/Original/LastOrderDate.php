<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC;
use ACP;
use Automattic;

class LastOrderDate extends AC\Column implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('last_payment_date')
             ->set_original(true);
    }

    public function export()
    {
        return new WC\Export\OrderSubscription\SubscriptionDate('last_order_date_created');
    }

}