<?php

namespace ACA\EC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\EC\ListScreen\Venue;
use WP_Screen;

class VenueFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'tribe_venue' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new Venue();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === 'tribe_venue';
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen('tribe_venue');
    }

}