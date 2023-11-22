<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicRedirect extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_redirect';
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Dynamic Redirect', 'dynamic-content-for-elementor');
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_redirect', ['label' => $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_form_redirect_condition_field', ['label' => __('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('Type here the ID of the form field to check, or leave empty to perform the redirect', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \false]]);
        $repeater_fields->add_control('dce_form_redirect_condition_status', ['label' => __('Condition Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['empty' => ['title' => __('Empty', 'dynamic-content-for-elementor'), 'icon' => 'eicon-circle-o'], 'valued' => ['title' => __('Valorized with any value', 'dynamic-content-for-elementor'), 'icon' => 'eicon-dot-circle-o'], 'lt' => ['title' => __('Less than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'gt' => ['title' => __('Greater than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'equal' => ['title' => __('Equal to', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'contain' => ['title' => __('Contains', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check']], 'default' => 'valued', 'toggle' => \false, 'label_block' => \true, 'condition' => ['dce_form_redirect_condition_field!' => '']]);
        $repeater_fields->add_control('dce_form_redirect_condition_value', ['label' => __('Condition Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('A value to compare the value of the field', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_redirect_condition_field!' => '', 'dce_form_redirect_condition_status' => ['lt', 'gt', 'equal', 'contain']]]);
        $repeater_fields->add_control('dce_form_redirect_to', ['label' => __('Redirect To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true, 'categories' => [TagsModule::POST_META_CATEGORY, TagsModule::TEXT_CATEGORY, TagsModule::URL_CATEGORY]], 'label_block' => \true, 'render_type' => 'none', 'classes' => 'elementor-control-direction-ltr']);
        $widget->add_control('dce_form_redirect_repeater', ['label' => __('Redirects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_redirect_to }}}', 'fields' => $repeater_fields->get_controls()]);
        // $widget->add_control(
        // 		'dce_form_redirect_help', [
        // 			'type' => \Elementor\Controls_Manager::RAW_HTML,
        // 			'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __( 'Need Help', 'dynamic-content-for-elementor' ) . ' <i class="eicon-help-o"></i></a></div>',
        // 			'separator' => 'before',
        // 		]
        // );
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $post_id = \intval($_POST['post_id']);
        $form_id = sanitize_text_field($_POST['form_id']);
        if (!empty($fields['submitted_on_id'])) {
            // force post for Dynamic Tags and Widgets
            $submitted_on_id = $fields['submitted_on_id'];
            global $post, $wp_query;
            $post = get_post($submitted_on_id);
            $wp_query->queried_object = $post;
            $wp_query->queried_object_id = $submitted_on_id;
        }
        $document = \Elementor\Plugin::$instance->documents->get($post_id);
        if ($document) {
            $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), $form_id);
            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($form);
            $settings = $widget->get_settings_for_display();
        } else {
            $settings = $record->get('form_settings');
        }
        $settings = Helper::get_dynamic_value($settings, $fields);
        $this->redirection($fields, $settings, $ajax_handler, $record);
    }
    protected function redirection($fields, $settings = null, $ajax_handler = null, $record = null)
    {
        foreach ($settings['dce_form_redirect_repeater'] as $mkey => $aredirect) {
            $condition_satisfy = \true;
            $conditional_field = $aredirect['dce_form_redirect_condition_field'];
            if (!empty($conditional_field)) {
                $field_value = $fields[$conditional_field];
                $condition_value = $aredirect['dce_form_redirect_condition_value'];
                switch ($aredirect['dce_form_redirect_condition_status']) {
                    case 'empty':
                        if ($field_value !== '') {
                            $condition_satisfy = \false;
                        }
                        break;
                    case 'valued':
                        if ($field_value === '') {
                            $condition_satisfy = \false;
                        }
                        break;
                    case 'lt':
                        if ($field_value === '' || $field_value >= $condition_value) {
                            $condition_satisfy = \false;
                        }
                        break;
                    case 'gt':
                        if ($field_value === '' || $field_value <= $condition_value) {
                            $condition_satisfy = \false;
                        }
                        break;
                    case 'equal':
                        if ($field_value != $condition_value) {
                            $condition_satisfy = \false;
                        }
                    case 'contain':
                        $field_type = Helper::get_field_type($conditional_field, $settings);
                        if ($field_type == 'checkbox') {
                            $field_value = Helper::str_to_array(', ', $field_value);
                        }
                        if (\is_array($field_value)) {
                            if (!\in_array($condition_value, $field_value)) {
                                $condition_satisfy = \false;
                            }
                        } else {
                            if (\strpos($field_value, $condition_value) === \false) {
                                $condition_satisfy = \false;
                            }
                        }
                        break;
                }
            }
            if ($condition_satisfy) {
                $redirect_to = $aredirect['dce_form_redirect_to'];
                $redirect_to = Helper::get_dynamic_value($redirect_to, $fields);
                if (!empty($redirect_to)) {
                    if (\filter_var($redirect_to, \FILTER_VALIDATE_URL)) {
                        $ajax_handler->add_response_data('redirect_url', $redirect_to);
                        return \true;
                    } else {
                        if (is_user_logged_in()) {
                            $hint = '<br>Hint: url must begin with http and params should be encoded with "<a target="_blank" href="https://www.php.net/manual/en/function.urlencode.php">urlencode</a>" filter, for example [form:name|urlencode]';
                        }
                        $ajax_handler->add_error_message('URL not valid: <a href="' . $redirect_to . '" target="_blank">' . $redirect_to . '</a>' . $hint);
                        return \false;
                    }
                }
            }
        }
        return \false;
    }
    public function on_export($element)
    {
        return $element;
    }
}
