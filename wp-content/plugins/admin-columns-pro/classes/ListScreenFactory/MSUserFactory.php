<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use ACP\ListScreen\MSUser;
use WP_Screen;

class MSUserFactory extends AC\ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'wp-ms_users' === $key;
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'users-network' === $screen->base && 'users-network' === $screen->id && $screen->in_admin('network');
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new MSUser();
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen('wp-ms_users');
    }

}