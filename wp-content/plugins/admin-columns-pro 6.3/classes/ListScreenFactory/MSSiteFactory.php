<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use ACP\ListScreen\MSSite;
use WP_Screen;

class MSSiteFactory extends AC\ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'wp-ms_sites' === $key;
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'sites-network' === $screen->base && 'sites-network' === $screen->id && $screen->in_admin('network');
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new MSSite();
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen('wp-ms-sites');
    }

}