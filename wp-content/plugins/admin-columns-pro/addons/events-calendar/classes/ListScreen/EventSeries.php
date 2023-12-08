<?php

namespace ACA\EC\ListScreen;

use ACA\EC\Column;
use ACP;

class EventSeries extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('tribe_event_series');

        $this->group = 'events-calendar';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\EventSeries\Events::class,
        ]);
    }

}