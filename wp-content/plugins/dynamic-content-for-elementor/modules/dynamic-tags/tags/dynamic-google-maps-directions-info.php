<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicGoogleMapsDirectionsInfo extends Data_Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-dynamic-google-maps-directions-info';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Map Info', 'dynamic-content-for-elementor');
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
        return ['base', 'text', 'number'];
    }
    /**
     * Get Value
     *
     * @param array<mixed> $options
     * @return string
     */
    public function get_value(array $options = [])
    {
        $map_name = $this->get_settings('map_name');
        if (empty($map_name) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return Helper::notice('', __('Please type a Map Name', 'dynamic-content-for-elementor'));
        }
        $loading_text = $this->get_settings('loading_text');
        $option = $this->get_settings('options');
        $data = \json_encode(['map_name' => $map_name, 'loading_text' => $loading_text, 'option' => $option]);
        return "<div data-tag-name='" . esc_attr($map_name) . "' id='dce-directions-info'><span data-directions='" . esc_attr($data) . "' class='distance dce-directions-info'>" . $loading_text . "</span></div>";
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('map_name', ['label' => __('Map Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('options', ['label' => __('Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'default' => 'km', 'options' => ['miles' => __('Distance in Miles', 'dynamic-content-for-elementor'), 'km' => __('Distance in Km', 'dynamic-content-for-elementor'), 'text' => __('Distance in Text', 'dynamic-content-for-elementor'), 'minutes' => __('Distance in Minutes', 'dynamic-content-for-elementor'), 'mode' => __('Travel Mode', 'dynamic-content-for-elementor')], 'label_block' => \true]);
        $this->add_control('loading_text', ['label' => __('Loading Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Loading...', 'dynamic-content-for-elementor'), 'label_block' => 'true']);
    }
}
