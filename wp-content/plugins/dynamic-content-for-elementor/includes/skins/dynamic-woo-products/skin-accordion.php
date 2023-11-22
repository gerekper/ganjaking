<?php

namespace DynamicContentForElementor\Includes\Skins;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Dynamic_Woo_Products_Skin_Accordion extends \DynamicContentForElementor\Includes\Skins\Skin_Accordion
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamic-woo-products/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamic-woo-products/section_dynamicposts/after_section_end', [$this, 'register_additional_controls'], 20);
    }
}
