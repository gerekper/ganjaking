<?php

namespace DynamicContentForElementor\Extensions;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Repeater;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use ElementorPro\Modules\Forms\Module as Form_Module;
use ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ConditionalFields extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    public $depended_scripts = ['dce-conditional-fields'];
    public $depended_styles = ['dce-conditional-fields'];
    public $conditional_actions = ['activecampaign', 'mailchimp', 'webhook', 'mailerlite', 'mailpoet'];
    /**
     * @var ExpressionLanguage
     */
    private $expression_language = null;
    public function get_name()
    {
        return 'dce_conditional_fields_v2';
    }
    public function get_label()
    {
        return __('Conditional Fields', 'dynamic-content-for-elementor');
    }
    /**
     * @return ExpressionLanguage
     */
    public function get_lang()
    {
        if ($this->expression_language === null) {
            $expressionLanguage = new ExpressionLanguage();
            $this->expression_language = $expressionLanguage;
            $expressionLanguage->register('in_array', function ($str) {
                return 'false';
            }, function ($arguments, $el, $arr) {
                if (!\is_array($arr)) {
                    return $el === $arr;
                }
                return \in_array($el, $arr, \true);
            });
            $expressionLanguage->register('to_number', function ($str) {
                return 'false';
            }, function ($arguments, $str) {
                $dec = \filter_var($str, \FILTER_VALIDATE_INT);
                if ($dec !== \false) {
                    return $dec;
                }
                $fl = \filter_var($str, \FILTER_VALIDATE_FLOAT);
                if ($fl !== \false) {
                    return $fl;
                }
                return 0;
            });
        }
        return $this->expression_language;
    }
    /**
     * Rewrite the expression so that each line are logically connected
     *  with an `and`.
     */
    private static function and_join_lines($expr)
    {
        $lines = \preg_split('/\\r\\n|\\r|\\n/', $expr);
        $lines = \array_filter($lines, function ($l) {
            return !\preg_match('/^\\s*$/', $l);
            // filter empty lines
        });
        return '(' . \implode(')&&(', $lines) . ')';
    }
    private static function are_conditions_enabled($field)
    {
        $enabled = $field['dce_field_conditions_mode'] === 'show' || $field['dce_field_conditions_mode'] === 'hide';
        return $enabled && !\preg_match('/^\\s*$/', $field['dce_conditions_expression']);
    }
    private function get_fields_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['form_fields'] as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[] = ['id' => $field['custom_id'], 'condition' => self::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode'], 'disableOnly' => $field['dce_conditions_disable_only'] === 'yes'];
            }
        }
        return $conditions;
    }
    private function get_submit_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['dce_conditional_validations'] as $validation) {
            $hide = $validation['hide_submit'];
            if ($validation['disabled'] !== 'yes' && $hide !== '' && $hide !== 'visible') {
                $conditions[] = [
                    'expression' => self::and_join_lines($validation['expression']),
                    // yes is the old value of the option
                    'hide' => $hide === 'yes' ? 'hide' : $hide,
                ];
            }
        }
        return $conditions;
    }
    public function print_js_error_notice()
    {
        echo '<div class="dce-conditions-js-error-notice elementor-message elementor-message-danger" style="display: none;">';
        if (current_user_can('administrator')) {
            echo __('Dynamic.ooo - Conditional Fields: a JS Error has been detected. This could be caused by a JS Optimizer plugin. Please read this <a href="https://dnmc.ooo/jserror">article</a>. This message is not visible to site visitors', 'dynamic-content-for-elementor');
        } else {
            echo __('A problem was detected in the following Form. Submitting it could result in errors. Please contact the site administrator.', 'dynamic-content-for-elementor');
        }
        // the message is hidden on page load to avoid flash of content. If
        // everything is ok the error is than deleted by the js file of
        // conditional fields. If not we want the error to appear:
        echo '</div>';
        echo <<<SCRIPT
\t\t\t<script>
\t\t\tsetTimeout(function() {
\t\t\t\tlet el = document.querySelector(".dce-conditions-js-error-notice");
\t\t\t\tif (el)
\t\t\t\t\tel.style.display = "block";
\t\t\t}, 2000);
\t\t\t</script>
SCRIPT;
    }
    public function add_assets_depends($instance, $form)
    {
        // fetch all the settings data we need to pass to the JavaScript code:
        $field_conditions = $this->get_fields_conditions($instance);
        $submit_conditions = $this->get_submit_conditions($instance);
        $enabled = \false;
        if (!empty($field_conditions)) {
            $form->add_render_attribute('wrapper', 'data-field-conditions', wp_json_encode($field_conditions));
            $enabled = \true;
        }
        if (!empty($submit_conditions)) {
            $form->add_render_attribute('wrapper', 'data-submit-conditions', wp_json_encode($submit_conditions));
            $enabled = \true;
        }
        if ($enabled) {
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $this->print_js_error_notice();
            }
            $field_ids = [];
            foreach ($instance['form_fields'] as $field) {
                $field_ids[] = $field['custom_id'];
            }
            $form->add_render_attribute('wrapper', 'data-field-ids', wp_json_encode($field_ids));
            foreach ($this->depended_scripts as $script) {
                $form->add_script_depends($script);
            }
            foreach ($this->depended_styles as $style) {
                $form->add_style_depends($style);
            }
        }
    }
    protected function add_actions()
    {
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'update_validation_controls']);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'update_max_submissions_controls']);
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
        // very low priority because it needs to fix validation of other validation hooks.
        add_action('elementor_pro/forms/validation', [$this, 'validation'], 1000, 2);
        foreach ($this->conditional_actions as $action) {
            add_action("elementor/element/form/section_{$action}/before_section_end", [$this, 'update_actions_controls']);
        }
        add_action('elementor_pro/forms/process', [$this, 'actions_validation'], 10, 2);
    }
    public function update_max_submissions_controls($widget)
    {
        $widget->start_controls_section('section_max_submissions', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Max Submissions', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_max_submissions_enabled', ['label' => __('Enable', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $widget->add_control('dce_max_submission_counter_name', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => __('Name of the Counter Field to be checked', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $widget->add_control('dce_max_sumbissions_limit', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => __('Max', 'dynamic-content-for-elementor'), 'default' => 100, 'description' => __('The limit the Counter should not be able to go past. Notice that in very rare circumstances the limit could actually be exceeded. This can happen when the counter value is close to the limit and simultaneous submit ara attempted.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'label_block' => \true]);
        $widget->add_control('dce_max_submissions_error_message', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => __('Error Message', 'dynamic-content-for-elementor'), 'default' => __('Too many submissions, sorry', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $widget->end_controls_section();
    }
    public function update_validation_controls($widget)
    {
        $widget->start_controls_section('section_conditional_validation', ['label' => '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Conditional Validation', 'dynamic-content-for-elementor')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('disabled', ['label' => __('Disable', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $repeater->add_control('expression', ['condition' => ['disabled!' => 'yes'], 'label' => __('Expression', 'dynamic-content-for-elementor'), 'description' => __('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool <a target="_blank" href="https://dnmc.ooo/condgen">Conditions Generator</a> to generate your conditions more easily.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label_block' => \true]);
        $repeater->add_control('error_message', ['condition' => ['disabled!' => 'yes'], 'label' => __('Error Message', 'dynamic-content-for-elementor'), 'default' => __('Form Validation Error', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $repeater->add_control('error_field_id', ['condition' => ['disabled!' => 'yes'], 'label' => __('Field ID to attach the error to (optional)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $repeater->add_control('hide_submit', ['condition' => ['disabled!' => 'yes'], 'label' => __('If this condition is not satisfied the Submit Button should be', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['visible' => esc_html__('Visible', 'dynamic-content-for-elementor'), 'hide' => esc_html__('Hidden', 'dynamic-content-for-elementor'), 'disable' => esc_html__('Disabled', 'dynamic-content-for-elementor')], 'default' => 'visible', 'label_block' => \true]);
        $widget->add_control('dce_conditional_validations', ['label' => __('Conditional Validations', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['disabled' => 'yes', 'error_message' => 'Your name should be Joe', 'expression' => 'name == "Joe"', 'error_field_id' => 'name']], 'title_field' => 'Condition {{ error_field_id }}', 'hide_submit' => 'no']);
        $widget->end_controls_section();
    }
    public function update_actions_controls(\Elementor\Controls_Stack $widget)
    {
        $action = $widget->get_current_section()['condition']['submit_actions'];
        $widget->add_control("dce_action_condition_{$action}_enabled", ['label' => '<span class="color-dce icon-dyn-logo-dce"></span> ' . __('Conditionally run action', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $widget->add_control("dce_action_condition_{$action}_expression", ['condition' => ["dce_action_condition_{$action}_enabled" => 'yes'], 'label' => __('Expression', 'dynamic-content-for-elementor'), 'description' => __('The action will only be run if this expression is true. One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool <a target="_blank" href="https://dnmc.ooo/condgen">Conditions Generator</a> to generate your conditions more easily.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label_block' => \true]);
    }
    public function update_fields_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_conditions_tab' => ['type' => 'tab', 'tab' => 'content', 'label' => __('Conditions', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => '!in', 'value' => ['hidden', 'step']]]], 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_conditions_tab'], 'dce_field_conditions_mode' => ['name' => 'dce_field_conditions_mode', 'label' => __('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['visible' => ['title' => __('Always Visible', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check'], 'show' => ['title' => __('Show if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => __('Hide if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'toggle' => \false, 'default' => 'visible', 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab'], 'dce_conditions_expression' => ['name' => 'dce_conditions_expression', 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label' => __('Conditions Expressions', 'dynamic-content-for-elementor'), 'description' => __('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool <a target="_blank" href="https://dnmc.ooo/condgen">Conditions Generator</a> to generate your conditions more easily.', 'dynamic-content-for-elementor'), 'placeholder' => "name == 'Joe'", 'condition' => ['dce_field_conditions_mode!' => 'visible'], 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab'], 'dce_conditions_disable_only' => ['name' => 'dce_conditions_disable_only', 'label' => __('Disable only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab', 'condition' => ['dce_field_conditions_mode!' => 'visible']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /** Disable Form action by overriding registrar */
    public function disable_actions(array $actions_to_disable)
    {
        /** @var Form_Module $module */
        $module = Form_Module::instance();
        $actions = $module->actions_registrar->get();
        foreach ($actions_to_disable as $a) {
            unset($actions[$a]);
        }
        $module->actions_registrar = new class($actions) extends Form_Actions_Registrar
        {
            private $override_items;
            public function __construct($items)
            {
                $this->override_items = $items;
            }
            public function get($id = null)
            {
                if (!$id) {
                    return $this->override_items;
                }
                return isset($this->override_items[$id]) ? $this->override_items[$id] : null;
            }
        };
    }
    /**
     * Check action validations and remove the action that should not run.
     *
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function actions_validation($record, $ajax_handler)
    {
        $disable = [];
        $values = $this->get_field_values($record);
        foreach ($this->conditional_actions as $ca) {
            if ($record->get_form_settings("dce_action_condition_{$ca}_enabled") === 'yes') {
                $expr = $record->get_form_settings("dce_action_condition_{$ca}_expression");
                $expr = self::and_join_lines($expr);
                try {
                    $res = $this->get_lang()->evaluate($expr, $values);
                } catch (\DynamicOOOS\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                    $ajax_handler->add_error_message(__('Conditional Action error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                    $ajax_handler->send();
                    return;
                }
                if (!$res) {
                    $disable[] = $ca;
                }
            }
        }
        if (!empty($disable)) {
            $this->disable_actions($disable);
        }
    }
    /**
     * Determine all field visibilities based on the conditions.
     */
    public function determine_visibilities($conditions, $values, $ajax_handler)
    {
        $visibility = [];
        // Assume they are all visible at the beginning:
        foreach ($conditions as $id => $_) {
            $visibility[$id] = \true;
        }
        foreach ($conditions as $id => $condition) {
            try {
                $res = $this->get_lang()->evaluate($condition['condition'], $values);
            } catch (\Throwable $e) {
                $msg = esc_html__('Conditional Fields error (field: %s)', 'dynamic-content-for-elementor');
                $ajax_handler->add_error_message(\sprintf($msg, $id));
                $ajax_handler->add_admin_error_message($e->getMessage());
                return \false;
            }
            $res = $condition['mode'] === 'show' ? $res : !$res;
            if (!$res) {
                // we don't want an inactive field value to influence
                // further conditions:
                $values[$id] = '';
            }
            $visibility[$id] = $res;
        }
        return $visibility;
    }
    /** Return an array with key field id and value its raw_value */
    private function get_field_values($record)
    {
        $raw_fields = $record->get_field([]);
        $values = [];
        foreach ($raw_fields as $field) {
            $values[$field['id']] = $field['raw_value'];
        }
        return $values;
    }
    // Returns true if there are errors on the form ajax
    // handler. Unfortunately this doesn't work if set_success is used
    // directly, however this does not occur anywhere neither in Elementor pro
    // nor DCE.
    private static function ajax_handler_has_errors($ajax_handler)
    {
        $has_error = \false;
        $has_error |= !empty($ajax_handler->errors);
        $has_error |= !empty($ajax_handler->messages['error']);
        $has_error |= !empty($ajax_handler->messages['admin_error']);
        return $has_error;
    }
    /**
     * @param Form_Record $record
     * @param Ajax_Handler $ajax_handler
     * @return void
     */
    public function max_submissions_validation($record, $ajax_handler)
    {
        if ($record->get_form_settings('dce_max_submissions_enabled') !== 'yes') {
            return;
        }
        $name = $record->get_form_settings('dce_max_submission_counter_name') ?? '';
        $res = $record->get_field(['id' => $name]);
        if (empty($res)) {
            $ajax_handler->add_admin_error_message(esc_html__('Cannot find the max submission counter field. Please just put the name of the field, not inside tags or shortcodes.', 'dynamic-content-for-elementor'))->send();
        }
        $counter = $res[$name];
        if ($counter['type'] !== 'dce_counter') {
            $ajax_handler->add_admin_error_message(esc_html__('Cannot find the max submission counter field. The field does not seems to be a counter.', 'dynamic-content-for-elementor'))->send();
        }
        $value = $counter['value'];
        if ($value >= $record->get_form_settings('dce_max_sumbissions_limit')) {
            $ajax_handler->add_error_message($record->get_form_settings('dce_max_submissions_error_message'))->send();
        }
    }
    /**
     * Remove validation errors related to fields that are required but
     * that have been hidden by a condition.
     */
    public function fix_validation($record, $ajax_handler)
    {
        $conditions = [];
        $values = $this->get_field_values($record);
        foreach ($record->get_form_settings('form_fields') as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[$field['custom_id']] = ['condition' => self::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode']];
            }
        }
        $visibilities = $this->determine_visibilities($conditions, $values, $ajax_handler);
        if ($visibilities === \false) {
            return;
        }
        foreach ($visibilities as $id => $visible) {
            if (!$visible) {
                $type = $record->get_field(['id' => $id])[$id]['type'];
                // counter value is always present because set by its validation function.
                if ($type !== 'dce_counter' && !empty($values[$id])) {
                    // this can happen because JS expressionlanguage work slightly differently than PHP:
                    $ajax_handler->add_admin_error_message(__('Conditional Fields Error: When you have a field where you can pick multiple items, like checkbox or the select field with multiple select active, you must use the operator in. If instead you have a field where you pick only one value (like Select) you most likely want to use == and not in. Check the docs if unsure.', 'dynamic-content-for-elementor'));
                } else {
                    // Remove potential validation error related to the field:
                    unset($ajax_handler->errors[$id]);
                }
            }
        }
        // if there are no errors then the form is actually good.
        if (!$this->ajax_handler_has_errors($ajax_handler)) {
            $ajax_handler->set_success(\true);
        }
    }
    public function conditional_validation($record, $ajax_handler)
    {
        $values = $this->get_field_values($record);
        $validations = $record->get_form_settings('dce_conditional_validations');
        foreach ($validations as $validation) {
            if ($validation['disabled'] !== 'yes') {
                try {
                    $res = $this->get_lang()->evaluate(self::and_join_lines($validation['expression']), $values);
                } catch (\DynamicOOOS\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                    $ajax_handler->add_error('*no-field*', 'error');
                    $ajax_handler->add_admin_error_message(__('Conditional validation error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                    return;
                }
                if (!$res) {
                    if ($validation['error_field_id']) {
                        $ajax_handler->add_error($validation['error_field_id'], $validation['error_message']);
                    } else {
                        $ajax_handler->add_error('*no-field*', 'error');
                        $ajax_handler->add_error_message($validation['error_message']);
                    }
                }
            }
        }
    }
    public function validation($record, $ajax_handler)
    {
        $this->conditional_validation($record, $ajax_handler);
        $this->max_submissions_validation($record, $ajax_handler);
        $this->fix_validation($record, $ajax_handler);
    }
}
