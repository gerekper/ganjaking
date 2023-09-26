<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC;
use ACP;

class Status extends AC\Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('status')
             ->set_original(true);
    }

    public function export()
    {
        return new WC\Export\Order\Status();
    }

    public function search()
    {
        return new WC\Search\Order\Status();
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select(wcs_get_subscription_statuses()),
            new WC\Editing\Storage\OrderSubscription\Status()
        );
    }

}