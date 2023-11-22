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
class LiveHtml extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $depended_scripts = ['dce-live-html'];
    public function __construct()
    {
        // Make it so elementor doesn't render the label.
        $field_type = $this->get_type();
        add_filter("elementor_pro/forms/render/item/{$field_type}", function ($item) {
            $item['field_label'] = '';
            return $item;
        }, 10, 1);
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
        return 'Live HTML';
    }
    public function get_label()
    {
        return __('Live HTML', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_live_html';
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
        $field_controls = ['dce_live_html' => ['name' => 'dce_live_html', 'label' => __('HTML', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::CODE, 'default' => 'Hi {{ form.name }}', 'language' => 'html', 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_live_html_real_time' => ['name' => 'dce_live_html_real_time', 'label' => __('Update on each Keypress', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not wait for the field to be blurred for the event to be triggered. Do it on each keypress.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $form->add_render_attribute('div' . $item_index, 'data-code', do_shortcode($item['dce_live_html']));
        $form->add_render_attribute('div' . $item_index, 'class', 'dce-live-html-wrapper');
        $form->add_render_attribute('div' . $item_index, 'data-real-time', $item['dce_live_html_real_time'] ?? 'no');
        echo '<div ' . $form->get_render_attribute_string('div' . $item_index) . '></div>';
    }
}
