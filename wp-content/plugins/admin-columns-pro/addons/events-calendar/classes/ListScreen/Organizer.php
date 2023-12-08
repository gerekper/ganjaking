<?php

namespace ACA\EC\ListScreen;

use ACA\EC\Column;
use ACP;

class Organizer extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('tribe_organizer');

        $this->group = 'events-calendar';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\Organizer\Email::class,
            Column\Organizer\Events::class,
            Column\Organizer\Phone::class,
            Column\Organizer\UpcomingEvent::class,
            Column\Organizer\Website::class,
        ]);
    }

}