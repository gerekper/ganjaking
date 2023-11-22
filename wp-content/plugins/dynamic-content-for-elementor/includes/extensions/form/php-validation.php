<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class PhpValidation extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private static $actions_added = \false;
    public $name = 'Custom PHP Validation for Elementor Pro Form';
    public static $depended_plugins = ['elementor-pro'];
    private $is_common = \false;
    public $has_action = \false;
    const CONTROL_VALIDATION_CODE = 'dce_custom_php_validation_code';
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', self::CONTROL_VALIDATION_CODE);
    }
    public function get_name()
    {
        return 'dce_custom_validation';
    }
    public function get_label()
    {
        return __('PHP Validation', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        if (self::$actions_added) {
            return;
        }
        self::$actions_added = \true;
        // low priority action because conditional fields (old version) reset the validation status:
        add_action('elementor_pro/forms/validation', [$this, 'validate_form'], 100, 2);
        add_action('elementor/element/form/section_form_options/after_section_start', [$this, 'add_controls_to_form']);
    }
    public function validate_form($record, $ajax_handler)
    {
        $enabled = $record->get_form_settings('dce_custom_php_validation_enabled');
        if ('yes' === $enabled) {
            $code = $record->get_form_settings('dce_custom_php_validation_code');
            try {
                $raw_fields = $record->get_field([]);
                $fields = [];
                foreach ($raw_fields as $id => $content) {
                    $fields[$id] = $content['value'];
                }
                // phpcs:ignore Squiz.PHP.Eval.Discouraged
                $result = eval($code);
                if (\is_string($result)) {
                    $ajax_handler->add_error('*no-field*', 'error');
                    $ajax_handler->add_error_message($result);
                } elseif (\is_array($result) && \count($result) === 2) {
                    $ajax_handler->add_error($result[0], $result[1]);
                } elseif ($result) {
                    $ajax_handler->add_error('*no-field*', 'error');
                    $ajax_handler->add_error_message(__('Generic Form Error', 'dynamic-content-for-elementor'));
                }
            } catch (\Throwable $e) {
                $ajax_handler->add_error('*no-field*', 'error');
                $ajax_handler->add_admin_error_message(__('Error while evaluating PHP validation code:', 'dynamic-content-for-elementor') . $e->getMessage());
            }
        }
    }
    public function add_controls_to_form($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $widget->add_control('dce_custom_php_validation_enabled', ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('PHP Validation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no']);
        $widget->add_control(self::CONTROL_VALIDATION_CODE, ['label' => __('PHP Validation Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php', 'default' => '', 'separator' => 'after', 'description' => __('Use the variable $fields to access fields values (eg $fields["field_id"]). The validation succeeds only if the PHP code does not return or return false . If the code returns a string, then the string is returned as an error. If it returns [ $field_id, $error_message ], the error message will be reported for the specific field.', 'dynamic-content-for-elementor'), 'condition' => ['dce_custom_php_validation_enabled' => 'yes']]);
    }
}
