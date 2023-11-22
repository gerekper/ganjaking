<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Counter extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = [];
    public $depended_styles = [];
    public function run_once()
    {
        // low priority because we want to update the counter after payments have been processed:
        add_action('elementor_pro/forms/process', [$this, 'update_counters'], 1000, 2);
        add_filter("elementor_pro/forms/render/item/{$this->get_type()}", function ($item, $item_index, $form) {
            // this is done to hide the label without using JS:
            if (($item['dce_counter_hide'] ?? 'no') === 'yes') {
                $form->add_render_attribute('field-group' . $item_index, 'style', 'display: none;');
            }
            return $item;
        }, 10, 3);
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function get_name()
    {
        return __('Counter', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_counter';
    }
    public function update_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['html_notice_value' => ['name' => 'html_notice_value', 'type' => Controls_Manager::RAW_HTML, 'raw' => \sprintf(esc_html__('Notice that the counter field is only increased after submit. The value shown in the page is not necessarily unique and %1$s does not represent %2$s the value the field will have while doing the Form Actions.', 'dynamic-content-for-elementor'), '<strong>', '</strong>'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_counter_start' => ['name' => 'dce_counter_start', 'type' => Controls_Manager::NUMBER, 'label' => esc_html__('Start counter at', 'dynamic-content-for-elementor'), 'default' => 0, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['dce_counter_use_other!' => 'yes', 'field_type' => $this->get_type()]], 'dce_counter_step' => ['name' => 'dce_counter_step', 'type' => Controls_Manager::TEXT, 'label' => __('Counter Step', 'dynamic-content-for-elementor'), 'default' => '1', 'tab' => 'content', 'description' => esc_html__('A token or shortcode for another field can also be used.', 'dynamic-content-for-elementor'), 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()], 'dynamic' => ['active' => \true]], 'dce_counter_hide' => ['name' => 'dce_counter_hide', 'type' => Controls_Manager::SWITCHER, 'label' => esc_html__('Hide the counter', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_counter_use_other' => ['name' => 'dce_counter_use_other', 'type' => Controls_Manager::SWITCHER, 'label' => esc_html__('Use another counter', 'dynamic-content-for-elementor'), 'description' => esc_html__('Do not create a new counter, use an already existing one', 'dynamic-content-for-elementor'), 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_counter_other_ref' => ['name' => 'dce_counter_other_ref', 'type' => Controls_Manager::TEXT, 'label' => esc_html__('Other Counter ID', 'dynamic-content-for-elementor'), 'description' => esc_html__('The id of the other counter, you can find it in the data-counter-id attribute of counter input.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['dce_counter_use_other' => 'yes', 'field_type' => $this->get_type()]], 'dce_counter_unique' => ['name' => 'dce_counter_unique', 'type' => Controls_Manager::SWITCHER, 'label' => esc_html__('Unique Counter', 'dynamic-content-for-elementor'), 'default' => 'yes', 'description' => esc_html__('This option affects the counter when the form is inside a template: ' . 'If on, the counter will have an unique value accross all occurences of the form. ' . 'If off, the counter will have a different value for each object the form is included in.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['dce_counter_use_other!' => 'yes', 'field_type' => $this->get_type()]]];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    private function get_meta_key($form_id, $field__id)
    {
        return "dce_counter_{$form_id}_{$field__id}";
    }
    public function get_post_id_and_meta($form_id, $settings, $from)
    {
        if ($settings['dce_counter_use_other'] === 'yes') {
            $ref = \explode(':', $settings['dce_counter_other_ref']);
            if (\count($ref) != 2) {
                return [null, "Invalid reference"];
            }
            $meta = 'dce_counter_' . $ref[0];
            $post_id = $ref[1];
        } else {
            $meta = $this->get_meta_key($form_id, $settings['_id']);
            if ($from === 'render') {
                $post_id = \ElementorPro\Core\Utils::get_current_post_id();
                if ($settings['dce_counter_unique'] !== 'yes' && is_singular()) {
                    $post_id = get_the_ID();
                }
            } elseif ($from === 'ajax') {
                $post_id = $_POST['post_id'];
                if ($settings['dce_counter_unique'] !== 'yes' && isset($_POST['queried_id'])) {
                    $post_id = $_POST['queried_id'];
                }
            }
        }
        return [$post_id, $meta];
    }
    public function render($item, $item_index, $form)
    {
        [$post_id, $meta] = $this->get_post_id_and_meta($form->get_id(), $item, 'render');
        if ($post_id === null) {
            $value = $meta;
        } else {
            $value = get_post_meta($post_id, $meta, \true);
            if ($value === \false || $value === '') {
                if ($item['dce_counter_use_other'] === 'yes') {
                    $value = esc_html__('Error: The other counter ID is invalid or it has not been initialized', 'dynamic-content-for-elementor');
                } else {
                    if (!\is_numeric($item['dce_counter_start'])) {
                        $value = __('Counter Start is not numeric.', 'dynamic-content-for-elementor');
                    } else {
                        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            // initializing in edit mode could create many useless post meta.
                            update_post_meta($post_id, $meta, $item['dce_counter_start']);
                        }
                        $value = $item['dce_counter_start'];
                    }
                }
            }
        }
        $form->add_render_attribute('input' . $item_index, 'class', 'elementor-field-textual');
        // the first part of the counter_id is the meta without its prefix:
        $meta_for_id = \substr($meta, \strlen('dce_counter_'));
        $form->add_render_attribute('input' . $item_index, 'data-counter-id', $meta_for_id . ':' . $post_id);
        $form->add_render_attribute('input' . $item_index, 'readonly', \true);
        $form->add_render_attribute('input' . $item_index, 'value', $value, \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
    public function validation($field, Form_Record $record, Ajax_Handler $ajax_handler)
    {
        $settings = \DynamicContentForElementor\Helper::get_form_field_settings($field['id'], $record);
        $form_id = $record->get_form_settings('id');
        [$post_id, $meta] = $this->get_post_id_and_meta($form_id, $settings, 'ajax');
        $value = get_post_meta($post_id, $meta, \true);
        if ($value === \false || $value === '') {
            $value = $settings['dce_counter_start'];
        }
        // temporary value, needed for max submissions:
        $record->update_field($field['id'], 'value', $value);
        $record->update_field($field['id'], 'raw_value', $value);
    }
    /**
     * Returns the final unique value of the field.
     */
    public function update_counter($post_id, $meta_key, $start, $step)
    {
        global $wpdb;
        $wpdb->query('start transaction');
        $curr = $wpdb->get_var($wpdb->prepare("select meta_value from {$wpdb->postmeta} where post_id = %d and meta_key = %s for update", $post_id, $meta_key));
        if ($curr === null) {
            $curr = $start;
            $res = $wpdb->insert($wpdb->postmeta, ['meta_value' => $curr + $step, 'post_id' => $post_id, 'meta_key' => $meta_key]);
        } else {
            $wpdb->update($wpdb->postmeta, ['meta_value' => $curr + $step], ['post_id' => $post_id, 'meta_key' => $meta_key]);
        }
        $wpdb->query('commit');
        return $curr;
    }
    public function get_step($step, $record)
    {
        $match = \false;
        if (\preg_match('/\\[\\s*field\\s*id\\s*=\\s*"([^"]+)"\\s*\\]/', $step, $matches)) {
            $match = $matches[1];
        } elseif (\preg_match('/\\[form:([^\\]]+)\\]/', $step, $matches)) {
            $match = $matches[1];
        }
        if ($match) {
            return $record->get_field(['id' => $match])[$match]['value'];
        }
        return $step;
    }
    public function update_counters($record, $ajax_handler)
    {
        $form_id = $record->get_form_settings('id');
        $fields = $record->get_form_settings('form_fields');
        foreach ($fields as $field) {
            if ($field['field_type'] !== $this->get_type()) {
                continue;
            }
            $step = $this->get_step($field['dce_counter_step'], $record);
            if (!\is_numeric($step)) {
                $ajax_handler->add_admin_error_message(__('Counter Step is not numeric.', 'dynamic-content-for-elementor'));
                return;
            }
            $start = $field['dce_counter_start'];
            [$post_id, $meta_key] = $this->get_post_id_and_meta($form_id, $field, 'ajax');
            $value = $this->update_counter($post_id, $meta_key, $start, $step);
            $record->update_field($field['custom_id'], 'value', $value);
            $record->update_field($field['custom_id'], 'raw_value', $value);
        }
    }
}
