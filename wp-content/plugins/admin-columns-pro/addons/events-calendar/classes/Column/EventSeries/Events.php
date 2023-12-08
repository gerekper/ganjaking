<?php

namespace ACA\EC\Column\EventSeries;

use AC;
use ACA\EC\Editing;
use ACA\EC\Export;
use ACP;

class Events extends AC\Column implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('events')
             ->set_original(true);
    }

    public function export()
    {
        return new Export\Model\EventSeries\Events();
    }

}