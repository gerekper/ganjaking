<?php

namespace ACA\ACF\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\ACF\ListScreen\FieldGroup;
use WP_Screen;

class FieldGroupFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return 'acf-field-group' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new FieldGroup();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === 'acf-field-group';
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new FieldGroup();
    }

}