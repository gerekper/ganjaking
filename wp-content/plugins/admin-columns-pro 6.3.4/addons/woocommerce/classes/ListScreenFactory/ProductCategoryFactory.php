<?php

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\WC\ListScreen\ProductCategory;
use ACP\ListScreen\Taxonomy;
use WP_Screen;

class ProductCategoryFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return Taxonomy::KEY_PREFIX . 'product_cat' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new ProductCategory();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'edit-tags' === $screen->base && 'product_cat' === $screen->taxonomy;
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new ProductCategory();
    }

}