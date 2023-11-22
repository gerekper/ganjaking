<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\ExtensionInfo;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicSelect extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    use ExtensionInfo;
    public $has_action = \false;
    public $depended_scripts = ['dce-dynamic-select'];
    public $depended_styles = [];
    public function get_type()
    {
        return 'dynamic_select';
    }
    public function get_name()
    {
        return 'Dynamic Select';
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_select_field_id' => ['name' => 'dce_select_field_id', 'label' => __('Reference Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \false], 'condition' => ['field_type' => $this->get_type()]], 'dce_dynamic_select_options' => ['name' => 'dce_dynamic_select_options', 'label' => __('Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'description' => __('Options are written as a normal select field. They are grouped according to the reference field value with sections starting with this value inside square brackets: <code>[value]</code>.', 'dynamic-content-for-elementor'), 'condition' => ['field_type' => $this->get_type()], 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs']];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $term =& $control_data['fields']['allow_multiple']['conditions']['terms'][0];
        $term['operator'] = 'in';
        $term['value'] = ['select', $this->get_type()];
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $i = $item_index;
        // The following is taken from Elementor code for making a select field.
        $form->add_render_attribute(['select-wrapper' . $i => ['class' => ['elementor-field', 'elementor-select-wrapper', esc_attr($item['css_classes'])]], 'select' . $i => ['name' => $form->get_attribute_name($item) . (!empty($item['allow_multiple']) ? '[]' : ''), 'id' => $form->get_attribute_id($item), 'class' => ['elementor-field-textual', 'elementor-size-' . $item['input_size']]]]);
        if ($item['required']) {
            $form->add_render_attribute('select' . $i, 'required', 'required');
            $form->add_render_attribute('select' . $i, 'aria-required', 'true');
        }
        if ($item['allow_multiple']) {
            $form->add_render_attribute('select' . $i, 'multiple');
            if (!empty($item['select_size'])) {
                $form->add_render_attribute('select' . $i, 'size', $item['select_size']);
            }
        }
        $lines = \preg_split("/\\r\\n|\\r|\\n/", $item['dce_dynamic_select_options']);
        if (!$lines) {
            return;
        }
        $options = ['' => []];
        $current_value = '';
        foreach ($lines as $line) {
            if (\preg_match('/^\\[([^\\]]+)\\]\\s*$/', $line, $matches)) {
                $current_value = $matches[1];
                $options[$current_value] = [];
            } elseif (!\preg_match('/^\\s*$/', $line)) {
                $options[$current_value][] = $line;
            }
        }
        $form->add_render_attribute('select' . $i, 'data-options', wp_json_encode($options));
        $form->add_render_attribute('select' . $i, 'data-field-id', $item['dce_select_field_id']);
        ?>
		<div <?php 
        echo $form->get_render_attribute_string('select-wrapper' . $i);
        ?>>
		<select <?php 
        echo $form->get_render_attribute_string('select' . $i);
        ?>>
		</select> </div> <?php 
    }
}
