<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Tilt extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['tilt-lib', 'dce-tilt'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_tilt', ['label' => __('Tilt', 'dynamic-content-for-elementor')]);
        $this->add_control('template', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('translatez_template', ['label' => __('Translate Z', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'max' => 200, 'step' => 1, 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .template-inner' => 'transform: translateZ({{VALUE}}px);']]);
        $this->add_control('tilt_maxtilt', ['label' => __('Max Tilt', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['min' => 0, 'max' => 10, 'step' => 1], 'frontend_available' => \true]);
        $this->add_control('tilt_perspective', ['label' => __('Perspective', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1000, 'min' => 0, 'max' => 2000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('tilt_scale', ['label' => __('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1, 'max' => 2, 'step' => 0.01]);
        $this->add_control('tilt_speed', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 1000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('tilt_transition', ['label' => __('Transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('tilt_reset', ['label' => __('Reset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('tilt_glare', ['label' => __('Glare', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('tilt_maxGlare', ['label' => __('Max Glare', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $template = $settings['template'];
        echo '<div class="dce_tilt">';
        echo '<div class="js-tilt">';
        if ($template != '') {
            echo '<div class="template-inner">' . do_shortcode('[dce-elementor-template id="' . $template . '"]') . '</div>';
        } else {
            echo '<div class="tilt-inner"></div>';
        }
        echo '</div>';
        echo '</div>';
    }
}
