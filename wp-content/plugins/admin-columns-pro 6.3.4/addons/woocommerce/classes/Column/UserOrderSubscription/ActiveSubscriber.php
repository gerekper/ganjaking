<?php

namespace ACA\WC\Column\UserOrderSubscription;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP\Export\Exportable;
use ACP\Search\Searchable;

class ActiveSubscriber extends AC\Column
    implements Searchable, Exportable
{

    public function __construct()
    {
        $this->set_type('woocommerce_active_subscriber')
             ->set_original(true);
    }

    public function search()
    {
        return new Search\UserOrderSubscription\ActiveSubscriber();
    }

    public function export()
    {
        return new Export\UserSubscription\ActiveSubscriber();
    }

}