<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACP;

class Order extends AC\Column
    implements ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('order_title')
             ->set_original(true);
    }

    public function export()
    {
        return new Export\ShopOrder\Order();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\ID();
    }

}