<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC\Export;
use ACP;

class OrderTitle extends AC\Column implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('order_title')
             ->set_original(true);
    }

    public function export()
    {
        return new Export\Order\OrderTitle();
    }

}