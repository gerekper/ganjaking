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
class Signature extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-signature-lib', 'dce-signature'];
    public function __construct()
    {
        add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['signature_save_to_file' => ['name' => 'signature_save_to_file', 'label' => __('Save to file', 'dynamic-content-for-elementor'), 'default' => 'yes', 'type' => Controls_Manager::SWITCHER, 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'signature_jpeg' => ['name' => 'signature_jpeg', 'label' => __('Transmit using JPEG', 'dynamic-content-for-elementor'), 'description' => __('Use this option if the signature does not appear in the PDF.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function add_control_section_to_form($element, $args)
    {
        $element->start_controls_section('dce_section_signature_buttons_style', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Signature', 'dynamic-content-for-elementor'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $element->add_responsive_control('signature_canvas_width', ['label' => __('Width of the Signature Pad', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => 400], 'range' => ['px' => ['min' => 1, 'max' => 800, 'step' => 5]], 'selectors' => ['{{WRAPPER}} .dce-signature-wrapper' => '--canvas-width: {{SIZE}}{{UNIT}};']]);
        $element->add_control('signature_canvas_border_radius', ['label' => __('Pad Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'size_units' => 'px'], 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-signature-canvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $element->add_control('signature_canvas_border_width', ['label' => __('Pad Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'size_units' => 'px'], 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .dce-signature-canvas' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_control('signature_canvas_background_color', ['label' => __('Pad Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .dce-signature-canvas' => 'background-color: {{VALUE}};']]);
        $element->add_control('signature_canvas_pen_color', ['label' => __('Pen Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000']);
        $element->end_controls_section();
    }
    public static function get_satisfy_dependencies()
    {
        return \true;
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Signature';
    }
    public function get_label()
    {
        return __('Form Signature', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_form_signature';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function render($item, $item_index, $form)
    {
        $settings = $form->get_settings_for_display();
        // We do not use type hidden so the browser will honor required:
        $hidden_css = 'width: 0; height: 0; opacity: 0; position: absolute; pointer-events: none;';
        $form->add_render_attribute('input' . $item_index, 'style', $hidden_css, \true);
        $form->add_render_attribute('signature-canvas' . $item_index, 'class', 'dce-signature-canvas');
        $form->add_render_attribute('signature-canvas' . $item_index, 'data-pen-color', $settings['signature_canvas_pen_color']);
        $form->add_render_attribute('signature-canvas' . $item_index, 'data-background-color', $settings['signature_canvas_background_color']);
        $form->add_render_attribute('signature-canvas' . $item_index, 'data-jpeg', $item['signature_jpeg']);
        $form->add_render_attribute('signature-canvas' . $item_index, 'style', 'width: var(--canvas-width); height: calc(var(--canvas-width) / 2); border-style: solid');
        $form->add_render_attribute('signature-canvas' . $item_index, 'width', '400');
        $form->add_render_attribute('signature-canvas' . $item_index, 'height', '200');
        $form->add_render_attribute('signature-wrapper' . $item_index, 'class', 'dce-signature-wrapper');
        $form->add_render_attribute('signature-wrapper' . $item_index, 'id', 'dce-signature-wrapper-' . $form->get_attribute_name($item));
        $form->add_render_attribute('signature-wrapper' . $item_index, 'style', 'width: 100%;');
        echo '<div ' . $form->get_render_attribute_string('signature-wrapper' . $item_index) . '>';
        echo '<div style="position: relative; display: inline-block;">';
        echo '<button type="button" class="dce-signature-button-clear" data-action="clear" style="right: 0; position: absolute;">❌</button>';
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<canvas ' . $form->get_render_attribute_string('signature-canvas' . $item_index) . '></canvas>';
        echo '</div></div>';
    }
    /**
     * validate uploaded file field
     *
     * @param array                $field
     * @param Classes\Form_Record  $record
     * @param Classes\Ajax_Handler $ajax_handler
     */
    public function validation($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $id = $field['id'];
        if ($field['required'] && $field['raw_value'] === '') {
            $ajax_handler->add_error($id, __('This signature field is required.', 'dynamic-content-for-elementor'));
        }
        if ($field['raw_value'] === '') {
            return;
        }
        if (!\preg_match('&^data:image/(jpeg|png);base64,[\\w\\d/+]+=*$&', $field['raw_value'])) {
            $ajax_handler->add_error($id, __('Invalid signature.', 'dynamic-content-for-elementor'));
        }
    }
    public function save_to_file($data, $dir_name, $extension, $ajax_handler)
    {
        $dir_abs_path = trailingslashit(wp_upload_dir()['basedir']) . 'dynamic/signatures/' . $dir_name;
        Helper::ensure_dir($dir_abs_path);
        // Code from Elementor Upload field:
        $filename = \uniqid() . '.' . $extension;
        $filename = wp_unique_filename($dir_abs_path, $filename);
        $new_file = trailingslashit($dir_abs_path) . $filename;
        if (\is_dir($dir_abs_path) && \is_writable($dir_abs_path)) {
            $res = \file_put_contents($new_file, $data);
            if ($res) {
                // Set correct file permissions.
                $perms = 0644;
                @\chmod($new_file, $perms);
                $url = wp_upload_dir()['baseurl'] . '/dynamic/signatures/' . trailingslashit($dir_name) . $filename;
                return ['url' => $url, 'loc' => $new_file];
            } else {
                $ajax_handler->add_error_message(esc_html__('There was an error while trying to save your signature.', 'dynamic-content-for-elementor'));
            }
        } else {
            $ajax_handler->add_admin_error_message(esc_html__('Signature save directory is not writable or does not exist.', 'dynamic-content-for-elementor'));
        }
    }
    public function process_field($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $value = $field['value'];
        if ($value === '') {
            return;
        }
        $settings = Helper::get_form_field_settings($field['id'], $record);
        if (($settings['signature_save_to_file'] ?? '') !== 'yes') {
            return;
        }
        \preg_match('&^data:image/(jpeg|png);base64,([\\w\\d/+]+=*)$&', $value, $matches);
        $extension = $matches[1];
        $encoded_image = $matches[2];
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
        $decoded_image = \base64_decode($encoded_image);
        $dir_name = $settings['_id'];
        if (!\preg_match('/[\\w\\d_]+/', $dir_name)) {
            $ajax_handler->add_admin_error_message(__('Invalid field ID', 'dynamic-content-for-elementor'));
            return;
        }
        list('url' => $url, 'loc' => $loc) = $this->save_to_file($decoded_image, $dir_name, $extension, $ajax_handler);
        $record->update_field($field['id'], 'value', $url);
        $record->update_field($field['id'], 'raw_value', $loc);
    }
}
