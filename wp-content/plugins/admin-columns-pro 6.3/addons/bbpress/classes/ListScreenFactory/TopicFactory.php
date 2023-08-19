<?php

namespace ACA\BbPress\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\BbPress\ListScreen\Topic;
use WP_Screen;

class TopicFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'topic' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new Topic();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === 'topic';
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new Topic();
    }

}