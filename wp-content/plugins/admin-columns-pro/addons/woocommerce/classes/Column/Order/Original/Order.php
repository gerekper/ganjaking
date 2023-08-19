<?php

namespace ACA\WC\Column\Order\Original;

use AC;
use ACA\WC;
use ACP;

class Order extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('order_number')
             ->set_original(true);
    }

    public function search()
    {
        return new WC\Search\Order\OrderId();
    }

    public function export()
    {
        return new WC\Export\Order\OrderTitle();
    }

}