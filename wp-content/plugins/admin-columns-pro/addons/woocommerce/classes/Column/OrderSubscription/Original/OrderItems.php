<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;

class OrderItems extends AC\Column implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('order_items')
             ->set_original(true);
    }

    public function export()
    {
        return new Export\OrderSubscription\OrderItems();
    }

}