<?php

namespace DynamicContentForElementor\Includes\Skins;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Woo_Products_Cart_Skin_Grid_Filters extends \DynamicContentForElementor\Includes\Skins\Skin_Grid_Filters
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-woo-products-cart/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-woo-products-cart/section_dynamicposts/after_section_end', [$this, 'register_additional_filters_controls'], 11);
        add_action('elementor/element/dce-woo-products-cart/section_dynamicposts/after_section_end', [$this, 'register_additional_grid_controls'], 20);
    }
    public function register_additional_filters_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        parent::register_additional_filters_controls($widget);
        $this->update_control('filters_taxonomy', ['default' => 'product_cat']);
    }
}
