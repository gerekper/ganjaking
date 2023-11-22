<?php

namespace DynamicContentForElementor\Includes\Skins;

use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Search_Page_Results_Skin_CrossroadsSlideshow extends \DynamicContentForElementor\Includes\Skins\Skin_CrossroadsSlideshow
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-search-results/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-search-results/section_dynamicposts/after_section_end', [$this, 'register_additional_crossroadsslideshow_controls']);
    }
}
