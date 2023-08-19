<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC;
use ACP;

class RecurringTotal extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('recurring_total')
             ->set_original(true);
    }

    public function search()
    {
        return new WC\Search\Order\Total();
    }

    public function export()
    {
        return new WC\Export\OrderSubscription\Total();
    }

}