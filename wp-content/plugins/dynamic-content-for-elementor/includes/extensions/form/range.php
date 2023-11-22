<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use ElementorPro\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Range extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $has_action = \false;
    public $depended_scripts = ['dce-range'];
    public $depended_styles = [];
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function __construct()
    {
        add_action('elementor/element/form/section_steps_style/after_section_end', [$this, 'add_style_controls']);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function get_name()
    {
        return __('Range', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_range';
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_range_min' => ['name' => 'dce_range_min', 'label' => __('Minimum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_range_max' => ['name' => 'dce_range_max', 'label' => __('Maximum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 100, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_range_step' => ['name' => 'dce_range_step', 'label' => __('Step', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_range_show_value' => ['name' => 'dce_range_show_value', 'label' => __('Show Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()]]], 'frontend_available' => \true, 'default' => 'yes', 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_range_text_before' => ['name' => 'dce_range_text_before', 'label' => __('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()], ['name' => 'dce_range_show_value', 'value' => 'yes']]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab'], 'dce_range_text_after' => ['name' => 'dce_range_text_after', 'label' => __('Text After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'conditions' => ['terms' => [['name' => 'field_type', 'value' => $this->get_type()], ['name' => 'dce_range_show_value', 'value' => 'yes']]], 'frontend_available' => \true, 'tabs_wrapper' => 'form_fields_tabs', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab']];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function add_style_controls($widget)
    {
        $widget->start_controls_section('dce_range_section_style', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Range', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_responsive_control('dce_range_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range input' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-field-type-dce_range input::-webkit-slider-thumb' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-field-type-dce_range input::-moz-range-thumb' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};']]);
        $widget->add_responsive_control('dce_range_opacity', ['label' => __('Opacity (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.01]], 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range input' => 'opacity: {{SIZE}};']]);
        $widget->add_control('dce_range_background', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range.elementor-field-group:not(.elementor-field-type-upload) input.elementor-field' => '-webkit-appearance: none; appearance: none; outline: none; background-color: {{VALUE}};']]);
        $widget->add_control('dce_range_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_control('dce_range_heading_slider', ['label' => __('Slider', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_control('dce_range_slider_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range input::-webkit-slider-thumb' => '-webkit-appearance: none; appearance: none; background-color: {{VALUE}} !important; cursor: pointer; min-height: 15px;', '{{WRAPPER}} .elementor-field-type-dce_range input::-moz-range-thumb' => '-webkit-appearance: none; appearance: none; background-color: {{VALUE}} !important; cursor: pointer; min-height: 15px;']]);
        $widget->add_control('dce_range_slider_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-field-type-dce_range input::-webkit-slider-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .elementor-field-type-dce_range input::-moz-range-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_control('dce_range_heading_show_value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_responsive_control('dce_range_value_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > p' => 'width: 100%; text-align: {{VALUE}};']]);
        $widget->add_control('dce_range_value_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > p' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_range_value_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > p']);
        $widget->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_range_value_shadow', 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > p']);
        $widget->add_control('dce_range_heading_title', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_responsive_control('dce_range_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > label.elementor-field-label' => 'width: 100%; text-align: {{VALUE}};']]);
        $widget->add_control('dce_range_title_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > label.elementor-field-label' => 'color: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'dce_range_title_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > label.elementor-field-label']);
        $widget->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'dce_range_text_shadow', 'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-dce_range > label.elementor-field-label']);
        $widget->end_controls_section();
    }
    public function render($item, $item_index, $form)
    {
        $form->set_render_attribute('input' . $item_index, 'type', 'range');
        $form->set_render_attribute('input' . $item_index, 'min', $item['dce_range_min'] ?? 0);
        $form->set_render_attribute('input' . $item_index, 'max', $item['dce_range_max'] ?? 100);
        $form->set_render_attribute('input' . $item_index, 'step', $item['dce_range_step'] ?? 1);
        $form->add_render_attribute('input' . $item_index, 'data-show-value', $item['dce_range_show_value'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-text-before', $item['dce_range_text_before'] ?? '');
        $form->add_render_attribute('input' . $item_index, 'data-text-after', $item['dce_range_text_after'] ?? '');
        $form->add_render_attribute('value' . $item_index, 'class', 'range-value');
        $value = empty($item['field_value']) ? '' : $item['field_value'];
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        if (!empty($item['dce_range_show_value'])) {
            echo '<p ' . $form->get_render_attribute_string('value' . $item_index) . '></p>';
        }
    }
    public function sanitize_field($value, $field)
    {
        return \floatval($value);
    }
}
