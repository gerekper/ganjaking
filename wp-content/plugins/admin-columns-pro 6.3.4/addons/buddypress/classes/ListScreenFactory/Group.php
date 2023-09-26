<?php

namespace ACA\BP\ListScreenFactory;

use AC\ListScreenFactory;
use ACA\BP\ListScreen;
use WP_Screen;

class Group extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'bp-groups' === $key;
    }

    protected function create_list_screen(string $key): \AC\ListScreen
    {
        return new ListScreen\Group();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->id === 'toplevel_page_bp-groups' && 'edit' !== filter_input(INPUT_GET, 'action');
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): \AC\ListScreen
    {
        return $this->create_list_screen('bp-groups');
    }

}