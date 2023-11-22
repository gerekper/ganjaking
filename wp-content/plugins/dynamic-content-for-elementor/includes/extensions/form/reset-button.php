<?php

namespace DynamicContentForElementor\Extensions;

use ElementorPro\Plugin as ProPlugin;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ResetButton extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public function __construct()
    {
        add_action('elementor/element/form/section_button_style/after_section_end', array($this, 'add_style'));
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return __('Reset', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'reset';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function render($item, $item_index, $form)
    {
        $form->add_render_attribute('input' . $item_index, 'data-field-id', $item['custom_id']);
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-button-reset');
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-button');
        if ($item['dce_reset_override_label'] === 'yes') {
            $form->add_render_attribute('input' . $item_index, 'value', $item['dce_reset_label'] ?? 'Reset');
        }
        if (!empty($item['button_size'])) {
            $form->add_render_attribute('input' . $item_index, 'class', 'elementor-size-' . $item['button_size']);
        }
        ?>
		<input <?php 
        $form->print_render_attribute_string('input' . $item_index);
        ?> >
		<?php 
    }
    public function update_controls($widget)
    {
        $elementor = ProPlugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_reset_override_label' => ['name' => 'dce_reset_override_label', 'label' => __('Override default label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_reset_label' => ['name' => 'dce_reset_label', 'label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_reset_override_label' => 'yes']]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function add_style($widget)
    {
        $widget->start_controls_section('section_reset_button_style', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Reset Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->start_controls_tabs('tabs_reset_button_style');
        $widget->start_controls_tab('tab_reset_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $widget->add_control('reset_button_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper).elementor-button.elementor-button-reset' => 'background-color: {{VALUE}} !important;']]);
        $widget->add_control('reset_button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-button.elementor-button-reset svg' => 'fill: {{VALUE}};']]);
        $widget->add_group_control(Group_Control_Typography::get_type(), ['name' => 'reset_button_typography', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'reset_button_border', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset']);
        $widget->add_control('reset_button_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $widget->add_control('reset_button_text_padding', ['label' => __('Text Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->end_controls_tab();
        $widget->start_controls_tab('tab_reset_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $widget->add_control('reset_button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'background-color: {{VALUE}} !important;']]);
        $widget->add_control('reset_button_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'color: {{VALUE}};']]);
        $widget->add_control('reset_button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'border-color: {{VALUE}};'], 'condition' => ['reset_button_border_border!' => '']]);
        $widget->end_controls_tab();
        $widget->end_controls_tabs();
        $widget->end_controls_section();
    }
}
