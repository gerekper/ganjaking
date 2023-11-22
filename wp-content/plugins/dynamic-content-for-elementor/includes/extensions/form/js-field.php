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
class JsField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $depended_scripts = ['dce-js-field'];
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'form_fields::dce_js_field_code');
        add_action('wp_enqueue_scripts', function () {
            wp_localize_script('dce-js-field', 'jsFieldLocale', ['syntaxError' => __('Your JS Field code contains errors, check the browser console!', 'dynamic-content-for-elementor'), 'returnError' => __('Your JS Field code should return a function.', 'dynamic-content-for-elementor')]);
        }, 100);
    }
    public function __construct()
    {
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
        return 'JS Field';
    }
    public function get_label()
    {
        return __('JS Field', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_js_field';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function update_controls($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['dce_js_field_code' => ['name' => 'dce_js_field_code', 'label' => __('JS Code', 'dynamic-content-for-elementor'), 'description' => __('Your code should return a function. This function will be called whenever a form input changes. It should return a new value for this field. Use the function <code>getField(fieldId)</code> to get a field current value. Notice that all fields are returned as either strings or arrays of strings.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::CODE, 'default' => 'return () => { return "Hello " + getField("name"); };', 'language' => 'javascript', 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_js_field_hide' => ['name' => 'dce_js_field_hide', 'label' => __('Hide', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not display the field in the form, use its value only in the Actions (like Email)', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_js_field_real_time' => ['name' => 'dce_js_field_real_time', 'label' => __('Update on each Keypress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not wait for the field to be blurred for the event to be triggered. Do it on each keypress.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $method = $form->get_settings('form_method');
        if ($method === 'post' || $method === 'get') {
            echo '<p><span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
            echo __('JS Field is not compatible with the Method Extension Post and Get options.', 'dynamic-content-for-elementor');
            echo '</span></p>';
            return;
        }
        if ($item['dce_js_field_hide'] === 'yes') {
            $form->add_render_attribute('input' . $item_index, 'data-hide', 'yes');
        }
        $form->add_render_attribute('input' . $item_index, 'data-field-code', $item['dce_js_field_code']);
        $form->add_render_attribute('input' . $item_index, 'data-real-time', $item['dce_js_field_real_time'] ?? 'no');
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-field-textual');
        $form->add_render_attribute('input' . $item_index, 'type', 'text');
        $form->add_render_attribute('input' . $item_index, 'readonly', '');
        echo '<input size="1"' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
}
