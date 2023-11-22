<?php

namespace DynamicContentForElementor\Extensions;

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
class FieldLength extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_length';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Length', 'dynamic-content-for-elementor');
    }
    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', [$this, '_render_form'], 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'text' || $afield['field_type'] == 'textarea') {
                    if (!empty($afield['field_maxlength'])) {
                        $content = \str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" maxlength="' . $afield['field_maxlength'] . '"', $content);
                    }
                    if (!empty($afield['field_minlength'])) {
                        $content = \str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" minlength="' . $afield['field_minlength'] . '"', $content);
                    }
                }
            }
        }
        return $content;
    }
    public function update_fields_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_maxlength' => ['name' => 'field_maxlength', 'label' => __('Max character length', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'separator' => 'before', 'min' => 0, 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => ['text', 'textarea']]]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted'], 'field_minlength' => ['name' => 'field_minlength', 'label' => __('Min character length', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => ['text', 'textarea']]]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
