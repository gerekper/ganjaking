<?php

namespace ACA\WC\Column\Order\Original;

use AC;
use ACA\WC;
use ACP;

class Status extends AC\Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('order_status')
             ->set_original(true);
    }

    public function search()
    {
        return new WC\Search\Order\Status();
    }

    public function export()
    {
        return new WC\Export\Order\Status();
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select(wc_get_order_statuses()),
            new WC\Editing\Storage\Order\Status()
        );
    }

}