<?php

namespace ACA\BP\ListScreenFactory;

use AC\ListScreenFactory;
use ACA\BP\ListScreen;
use WP_Screen;

class Email extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'bp-email' === $key;
    }

    protected function create_list_screen(string $key): \AC\ListScreen
    {
        return new ListScreen\Email();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === 'bp-email';
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): \AC\ListScreen
    {
        return $this->create_list_screen('bp-email');
    }

}