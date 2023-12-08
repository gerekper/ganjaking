<?php

namespace ACA\EC\ListScreen;

use ACA\EC\Column;
use ACP;

class Venue extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('tribe_venue');

        $this->group = 'events-calendar';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\Venue\Address::class,
            Column\Venue\City::class,
            Column\Venue\Country::class,
            Column\Venue\Events::class,
            Column\Venue\Phone::class,
            Column\Venue\PostalCode::class,
            Column\Venue\StateProvince::class,
            Column\Venue\UpcomingEvent::class,
            Column\Venue\Website::class,
        ]);
    }

}