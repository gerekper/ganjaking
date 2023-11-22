<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ShortcodeWithTokens extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_doshortcode', ['label' => $this->get_title()]);
        $this->add_control('doshortcode_string', ['label' => $this->get_title(), 'type' => Controls_Manager::TEXTAREA, 'description' => __('Example:', 'dynamic-content-for-elementor') . ' [gallery ids="[post:custom-meta]"]']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['doshortcode_string'])) {
            return;
        }
        echo do_shortcode(Helper::get_dynamic_value($settings['doshortcode_string']));
    }
}
