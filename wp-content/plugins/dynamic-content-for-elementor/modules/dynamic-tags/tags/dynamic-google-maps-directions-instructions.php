<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicGoogleMapsDirectionsInstructions extends Data_Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-dynamic-google-maps-directions-instructions';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Map Instructions', 'dynamic-content-for-elementor');
    }
    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'dce-dynamic-google-maps-directions';
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * Get value
     *
     * @param array<mixed> $options
     * @return string
     */
    public function get_value(array $options = [])
    {
        $map_name = $this->get_settings('map_name');
        $loading_text = $this->get_settings('loading_text');
        if (empty($map_name) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return Helper::notice('', __('Please type a Map Name', 'dynamic-content-for-elementor'));
        }
        return "<div data-tag-name='" . esc_attr($map_name) . "' id='dce-gm-directions-instructions'><span id='print_instructions' data-instructions='" . esc_attr($map_name) . "' class='distance dce-gm-directions-instructions'>" . $loading_text . "</span></div>";
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('map_name', ['label' => __('Map Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ""]);
        $this->add_control('loading_text', ['label' => __('Loading Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Loading...', 'dynamic-content-for-elementor'), 'label_block' => 'true']);
    }
}
