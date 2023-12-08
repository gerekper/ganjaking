<?php

namespace ACA\EC\Column\Event;

use ACA\EC\Column\Meta;
use ACA\EC\Editing;
use ACA\EC\Export;
use ACA\EC\Search;
use ACP;

class AllDayEvent extends Meta
    implements ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('column-ec-event_alldayevent');
        $this->set_label(__('All Day Event', 'the-events-calendar'));

        parent::__construct();
    }

    public function get_meta_key()
    {
        return '_EventAllDay';
    }

    public function get_value($id)
    {
        return ac_helper()->icon->yes_or_no('1' === $this->get_raw_value($id));
    }

    public function editing()
    {
        return new Editing\Service\Event\AllDayEvent();
    }

    public function export()
    {
        return new Export\Model\Event\AllDayEvent($this);
    }

    public function search()
    {
        return new Search\Event\AllDayEvent();
    }

}