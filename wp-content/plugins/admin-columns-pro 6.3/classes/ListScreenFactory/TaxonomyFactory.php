<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use ACP\ListScreen\Taxonomy;
use LogicException;
use WP_Screen;

class TaxonomyFactory extends AC\ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return null !== $this->get_taxonomy($key);
    }

    private function get_taxonomy(string $key): ?string
    {
        if ( ! ac_helper()->string->starts_with($key, 'wp-taxonomy_')) {
            return null;
        }

        $taxonomy = substr($key, 12);

        return taxonomy_exists($taxonomy)
            ? $taxonomy
            : null;
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'edit-tags' === $screen->base && $screen->taxonomy && $screen->taxonomy === filter_input(
                INPUT_GET,
                'taxonomy'
            );
    }

    protected function create_list_screen(string $key): ListScreen
    {
        $taxonomy = $this->get_taxonomy($key);

        if ( ! $taxonomy) {
            throw new LogicException('Invalid taxonomy');
        }

        return new Taxonomy($taxonomy);
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new Taxonomy($screen->taxonomy);
    }

}