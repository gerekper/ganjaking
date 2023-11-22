<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\Forms\Fields;
use Elementor\Widget_Base;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class RegexField extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_regex';
    }
    public function get_label()
    {
        return __('Regex Field for Elementor Pro Form', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'text' && !empty($afield['field_regex'])) {
                    $content = \str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" data-regex="true" pattern="' . esc_attr($afield['field_regex']) . '"', $content);
                }
            }
        }
        return $content;
    }
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_regex' => ['name' => 'field_regex', 'label' => __('Regex', 'dynamic-content-for-elementor'), 'description' => __('A regular expression is a sequence of characters that define a pattern. Use it to restrict the characters permitted on this field.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'separator' => 'before', 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => ['text', 'textarea', 'email', 'url', 'password']]]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
