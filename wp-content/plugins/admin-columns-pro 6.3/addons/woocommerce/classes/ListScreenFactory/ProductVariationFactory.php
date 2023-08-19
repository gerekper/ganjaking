<?php

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\WC\ListScreen\ProductVariation;
use WP_Screen;

class ProductVariationFactory extends ListScreenFactory\BaseFactory
{

    private const KEY = 'product_variation';

    private $column_config;

    public function __construct(array $column_config)
    {
        $this->column_config = $column_config;
    }

    public function can_create(string $key): bool
    {
        return self::KEY === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new ProductVariation($this->column_config);
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === self::KEY;
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen(self::KEY);
    }

}