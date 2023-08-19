<?php

namespace ACA\EC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\EC\ListScreen\Organizer;
use WP_Screen;

class OrganizerFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'tribe_organizer' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new Organizer();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === 'tribe_organizer';
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen('tribe_organizer');
    }

}