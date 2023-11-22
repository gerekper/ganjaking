<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Tooltip extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $name = 'Tooltip';
    public $has_controls = \true;
    public function get_script_depends()
    {
        return ['dce-tippy', 'dce-tooltip'];
    }
    public function get_style_depends()
    {
        return ['dce-tooltip'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        \DynamicContentForElementor\Plugin::instance()->wpml->add_extensions_fields(['dce_tooltip_content' => ['field' => 'dce_tooltip_content', 'type' => 'Tooltip Content', 'editor_type' => 'TEXT']]);
    }
    private function add_controls($element, $args)
    {
        $element->add_control('dce_enable_tooltip', ['label' => __('Tooltip', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $element->add_control('dce_tooltip_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_arrow', ['label' => __('Arrow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => 'true', 'default' => 'yes', 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_follow_cursor', ['label' => __('Follow Cursor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['false' => __('No', 'dynamic-content-for-elementor'), 'true' => __('Yes', 'dynamic-content-for-elementor'), 'horizontal' => __('Horizontal', 'dynamic-content-for-elementor'), 'vertical' => __('Vertical', 'dynamic-content-for-elementor'), 'initial' => __('Initial', 'dynamic-content-for-elementor')], 'default' => 'false', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_responsive_control('dce_tooltip_max_width', ['label' => __('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 80, 'max' => 800, 'step' => 10]], 'devices' => Helper::get_active_devices_list(), 'desktop_default' => ['size' => 200, 'unit' => 'px'], 'label_block' => \true, 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_touch', ['label' => __('Touch Devices', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['true' => __('Enable', 'dynamic-content-for-elementor'), 'false' => __('Disable', 'dynamic-content-for-elementor'), 'hold' => __('Require pressing and holding the screen to show it', 'dynamic-content-for-elementor')], 'default' => 'true', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'background-color: {{VALUE}};', ".tippy-box[data-theme~='theme_{{ID}}'] > .tippy-arrow:before" => 'border-top-color: {{VALUE}} !important;'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_tooltip_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => ".tippy-box[data-theme~='theme_{{ID}}']", 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'color: {{VALUE}};'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => [".tippy-box[data-theme~='theme_{{ID}}']" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'], 'condition' => ['dce_enable_tooltip' => 'yes']]);
        $element->add_control('dce_tooltip_zindex', ['label' => __('Z-Index', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '9999', 'frontend_available' => \true, 'condition' => ['dce_enable_tooltip' => 'yes']]);
    }
    protected function add_actions()
    {
        add_action('elementor/element/common/dce_section_tooltip_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
        add_action('elementor/widget/render_content', [$this, 'render_template'], 9, 2);
    }
    public function render_template($content, $widget)
    {
        $settings = $widget->get_settings_for_display();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || !empty($settings['dce_enable_tooltip'])) {
            $this->enqueue_all();
        }
        return $content;
    }
}
