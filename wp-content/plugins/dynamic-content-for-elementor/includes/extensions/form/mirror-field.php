<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
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
class MirrorField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $depended_scripts = ['dce-mirror-field'];
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Mirror';
    }
    public function get_label()
    {
        return __('mirror', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_mirror_field';
    }
    public function get_style_depends()
    {
        return [];
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_mirror_source' => ['name' => 'dce_mirror_source', 'label' => __('Source Field', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_mirror_hide' => ['name' => 'dce_mirror_hide', 'label' => __('Hide Field', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_mirror_modifiable' => ['name' => 'dce_mirror_modifiable', 'label' => esc_html__('Modifiable', 'dynamic-content-for-elementor'), 'description' => esc_html__('If modifiable the mirroring will stop after the user makes the first direct change to the field', 'dnamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_mirror_real_time' => ['name' => 'dce_mirror_real_time', 'label' => __('Update on each Keypress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not wait for the field to be blurred for the event to be triggered. Do it on each keypress.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $method = $form->get_settings('form_method');
        if ($method === 'post' || $method === 'get') {
            echo '<p><span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
            echo __('Mirror is not compatible with the Method Extension Post and Get options.', 'dynamic-content-for-elementor');
            echo '</span></p>';
            return;
        }
        if ($item['dce_mirror_hide'] === 'yes') {
            $form->add_render_attribute('input' . $item_index, 'data-hide', 'yes');
        }
        $source = $item['dce_mirror_source'];
        if (\preg_match('/"(.*?)"/', $source, $matches)) {
            // match id in shortcode.
            $source = $matches[1];
        } elseif (\preg_match('/:(.*?)\\]/', $source, $matches)) {
            // match id in token.
            $source = $matches[1];
        }
        $form->add_render_attribute('input' . $item_index, 'data-source-field-id', $source);
        $form->add_render_attribute('input' . $item_index, 'data-real-time', $item['dce_mirror_real_time'] ?? 'no');
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-field-textual');
        $form->add_render_attribute('input' . $item_index, 'type', 'text');
        if ($item['dce_mirror_modifiable'] !== 'yes') {
            $form->add_render_attribute('input' . $item_index, 'readonly', '');
        }
        echo '<input size="1"' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
}
