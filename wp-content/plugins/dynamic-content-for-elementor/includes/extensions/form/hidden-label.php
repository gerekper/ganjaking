<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class HiddenLabel extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $has_action = \false;
    public $depended_scripts = ['dce-hidden-label'];
    public $depended_styles = ['dce-hidden-label'];
    public function get_type()
    {
        return 'hidden_label';
    }
    public function get_name()
    {
        return 'Hidden Label';
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
        $field_controls = ['dce_hidden_label_id' => ['name' => 'dce_hidden_label_id', 'label' => __('Field ID', 'dynamic-content-for-elementor'), 'description' => __('The ID of the field we should get the selected choice label from. ', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \false], 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('input' . $item_index, 'size', '1', \true);
        $form->add_render_attribute('input' . $item_index, 'data-field-id', $item['dce_hidden_label_id'], \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
}
