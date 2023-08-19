<?php

declare(strict_types=1);

namespace ACA\BeaverBuilder;

use AC;
use AC\ListScreen;
use ACA\BeaverBuilder\ListScreen\Template;
use WP_Screen;

abstract class ListScreenFactory extends AC\ListScreenFactory\BaseFactory
{

    protected function create_list_screen(string $key): ListScreen
    {
        return new Template($this->get_page(), $this->get_label());
    }

    abstract protected function get_label(): string;

    abstract protected function get_page(): string;

    public function can_create(string $key): bool
    {
        return $key === Template::POST_TYPE . $this->get_page() && post_type_exists(Template::POST_TYPE);
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'edit' === $screen->base
               && $screen->post_type
               && 'edit-' . $screen->post_type === $screen->id
               && $this->get_page() === filter_input(INPUT_GET, 'fl-builder-template-type');
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen($screen->post_type);
    }

}