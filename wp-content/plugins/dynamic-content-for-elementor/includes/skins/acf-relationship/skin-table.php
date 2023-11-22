<?php

namespace DynamicContentForElementor\Includes\Skins;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Acf_Relationship_Skin_Table extends \DynamicContentForElementor\Includes\Skins\Skin_Table
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-acf-relationship/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-acf-relationship/section_dynamicposts/after_section_end', [$this, 'register_additional_table_controls']);
    }
}
