<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC;
use ACP;

class Orders extends AC\Column implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('orders')
             ->set_original(true);
    }

    public function export()
    {
        return new WC\Export\StrippedTableValue($this);
    }

}