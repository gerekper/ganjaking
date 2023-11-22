<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Save extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    public $action_priority = 2;
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_save_type_obj_id');
        $save_guard->register_unsafe_control('form', 'dce_form_save_type_user_role');
        $save_guard->register_unsafe_control('form', 'dce_form_save_metas');
        $save_guard->register_unsafe_control('form', 'dce_form_save_override');
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
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_save';
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
        return '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Save', 'dynamic-content-for-elementor');
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
        $roles = Helper::get_roles(\false, \true);
        $post_types = Helper::get_public_post_types();
        $taxonomies = Helper::get_taxonomies();
        $widget->start_controls_section('section_dce_form_save', ['label' => $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this action.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        $widget->add_control('dce_form_save_type', ['label' => __('Save fields as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['post' => ['title' => __('Post', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'], 'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user'], 'term' => ['title' => __('Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tag'], 'option' => ['title' => __('Option', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check']], 'default' => 'post', 'toggle' => \false, 'label_block' => 'false']);
        if (!get_option('users_can_register')) {
            $widget->add_control('user_can_register_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('User registration is currently disabled. Please enable it in WordPress Settings - General - Membership.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_form_save_type' => 'user']]);
        }
        $widget->add_control('dce_form_save_ignore_empty', ['label' => __('Ignore Empty fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not save the field if its value is empty to limit DB size consumption', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_save_file', ['label' => __('Save Files as Media', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Create a Media and save its ID instead of the URL', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_save_array', ['label' => __('Save Multiple as Array', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Save Files, Select and Checkboxes submitted values as Array instead of a comma to separate values string', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_save_redirect', ['label' => __('Redirect to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Go to new object page after saving it', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type!' => 'option']]);
        $widget->add_control('dce_form_save_anonymous', ['label' => __('Anonymous data', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Do not save User info for Privacy, like IP, referrer and current ID', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_save_override', ['label' => __('Update existent data', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('All data will be updated and all previous data will be overwritten', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_save_type_obj_id', ['label' => __('ID to update', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \true, 'description' => __('ID of the Object to update or leave it empty for Current.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_override!' => '']]);
        $widget->add_control('dce_form_save_type_post_post', ['label' => __('Find Post to update', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select a Post or leave it empty for the Current one', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'description' => __('Search Post by Title or leave it empty for Current Post.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'post', 'dce_form_save_override!' => '', 'dce_form_save_type_obj_id' => '']]);
        $widget->add_control('dce_form_save_type_user_user', ['label' => __('Find User to update', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Find a User', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'users', 'description' => __('Search User or leave it empty for Current User.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override!' => '', 'dce_form_save_type_obj_id' => '']]);
        $widget->add_control('dce_form_save_type_term_term', ['label' => __('Find Term to update', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Find a Term', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'description' => __('Search Term or leave it empty for Current Term.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'term', 'dce_form_save_override!' => '', 'dce_form_save_type_obj_id' => '']]);
        $widget->add_control('dce_form_save_metas', ['label' => __('Form fields to save as meta', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'name,message', 'description' => __('Type the field IDs here, separated by a comma. The field ID must be identical to the field name in your meta (i.e. your custom fields). If you want to save all fields, leave this empty', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'condition' => ['dce_form_save_type!' => 'option']]);
        $widget->add_control('dce_form_save_type_post_title', ['label' => __('Post Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Form Entry by', 'dynamic-content-for-elementor') . ' [field id="name"]', 'description' => __('You can use static text, field shortcode, tokens or mixed. Leave it empty for random values', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'post'], 'label_block' => 'true', 'separator' => 'before']);
        $widget->add_control('dce_form_save_type_post_content', ['label' => __('Post Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field id="message"]', 'description' => __('Can use static text, field shortcode, and tokens', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'post'], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $post_types, 'default' => '', 'condition' => ['dce_form_save_type' => 'post', 'dce_form_save_override' => ''], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_post_term', ['label' => __('Post Term', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('All terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'condition' => ['dce_form_save_type' => 'post', 'dce_form_save_override' => '']]);
        $widget->add_control('dce_form_save_type_post_status', ['label' => __('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => get_post_stati(), 'default' => 'publish', 'toggle' => \false, 'label_block' => 'true', 'condition' => ['dce_form_save_type' => 'post', 'dce_form_save_override' => '']]);
        $widget->add_control('dce_form_save_parent', ['label' => __('Current Post as Parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_save_type' => 'post', 'dce_form_save_override' => '']]);
        $widget->add_control('dce_form_save_type_user_username', ['label' => __('Username', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('Use field Shortcode for Username or leave it empty for a random value', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => ''], 'label_block' => 'true', 'separator' => 'before']);
        $widget->add_control('dce_form_save_type_user_email', ['label' => __('User Email', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[form:email]', 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => ''], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_user_pass', ['label' => __('User Password', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[form:user_pass]', 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => ''], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_user_role', ['label' => __('User Role', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $roles, 'default' => 'subscriber', 'condition' => ['dce_form_save_type' => 'user'], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_user_role_mode', ['label' => __('Add/Set Role', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ignore' => __('Ignore', 'dynamic-content-for-elementor'), 'add' => __('Add', 'dynamic-content-for-elementor'), 'set' => __('Set', 'dynamic-content-for-elementor')], 'default' => 'ignore', 'toggle' => \false, 'label_block' => 'true', 'description' => __('Set a unique role or add to existent user roles', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override!' => '']]);
        $widget->add_control('dce_form_save_type_user_login', ['label' => __('Auto Login', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('The new user will be automatically logged in and its data will be available when the next page is loaded', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => '']]);
        $default_message = \ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SUBSCRIBER_ALREADY_EXISTS, array());
        $widget->add_control('dce_form_save_type_user_error', ['label' => __('User registration Error Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => $default_message, 'label_block' => \true, 'separator' => 'before', 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => '']]);
        $widget->add_control('dce_form_save_type_user_error_stop', ['label' => __('Stop Actions on Error', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Stop Dynamic.ooo form actions (it doesn\'t stop Elementor actions) on Error,  username or email are not valid', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'user', 'dce_form_save_override' => '']]);
        $widget->add_control('dce_form_save_type_term_name', ['label' => __('Term Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Term [field id="name"]', 'description' => __('Can use static text, field Shortcode, and Tokens. Leave it empty for random values', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'term'], 'label_block' => 'true', 'separator' => 'before']);
        $widget->add_control('dce_form_save_type_term_description', ['label' => __('Term Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field id="message"]', 'description' => __('You can use text, Shortcodes and Tokens', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_save_type' => 'term'], 'label_block' => 'true']);
        $widget->add_control('dce_form_save_type_term_taxonomy', ['label' => __('Term Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $taxonomies, 'default' => 'category', 'condition' => ['dce_form_save_type' => 'term'], 'label_block' => 'true']);
        //
        // $widget->add_control(
        // 		'dce_form_save_help', [
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
        $settings = $record->get('form_settings');
        $this->save($fields, $settings, $ajax_handler);
    }
    private function save($record, $settings = null, $ajax_handler = null)
    {
        $obj_id = \false;
        $fields = array();
        if (\is_object($record)) {
            // from add action
            $data = $record->get_formatted_data(\true);
            foreach ($data as $label => $value) {
                $fields[$label] = sanitize_text_field($value);
            }
        } else {
            // from form extension
            $fields = $record;
        }
        // get raw data (html non stripped tags)
        foreach ($settings['form_fields'] as $fkey => $afield) {
            if ($afield['field_type'] == 'textarea' && !empty($afield['field_wysiwyg'])) {
                if (!empty($_POST['form_fields'][$afield['custom_id']])) {
                    $fields[$afield['custom_id']] = sanitize_text_field($_POST['form_fields'][$afield['custom_id']]);
                }
            }
        }
        if ($settings['dce_form_save_ignore_empty']) {
            $tmp = array();
            if (!empty($fields) && \is_array($fields)) {
                foreach ($fields as $akey => $adata) {
                    if ($adata != '') {
                        $tmp[$akey] = $adata;
                    }
                }
            }
            $fields = $tmp;
        }
        if (\is_object($record)) {
            $fields['form_name'] = $record->get_form_settings('form_name');
        } else {
            $fields['form_name'] = $settings['form_name'];
        }
        if (!empty($settings['dce_form_save_type_obj_id'])) {
            $settings['dce_form_save_type_obj_id'] = Helper::get_dynamic_value($settings['dce_form_save_type_obj_id'], $fields);
        }
        // Insert the post into the database
        // https://developer.wordpress.org/reference/functions/wp_insert_post/
        // https://developer.wordpress.org/reference/functions/wp_insert_user/
        // https://developer.wordpress.org/reference/functions/wp_insert_term/
        switch ($settings['dce_form_save_type']) {
            case 'post':
                if ($settings['dce_form_save_type_post_title']) {
                    $settings['dce_form_save_type_post_title'] = Helper::get_dynamic_value($settings['dce_form_save_type_post_title'], $fields);
                }
                if ($settings['dce_form_save_type_post_content']) {
                    $settings['dce_form_save_type_post_content'] = Helper::get_dynamic_value($settings['dce_form_save_type_post_content'], $fields);
                }
                $db_ins = array();
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        if (!Helper::is_post_meta($akey)) {
                            if (empty($db_ins[$akey])) {
                                $db_ins[$akey] = $adata;
                            }
                            unset($fields[$akey]);
                        }
                    }
                }
                if ($settings['dce_form_save_type_post_title']) {
                    $db_ins['post_title'] = $settings['dce_form_save_type_post_title'];
                }
                if ($settings['dce_form_save_type_post_content']) {
                    $db_ins['post_content'] = $settings['dce_form_save_type_post_content'];
                }
                // get ID
                $obj_id = $fields['submitted_on_id'];
                if ($settings['dce_form_save_type_obj_id']) {
                    $obj_id = $this->get_obj_id($settings['dce_form_save_type_obj_id'], 'post', $ajax_handler);
                    if (!$obj_id) {
                        return \false;
                    }
                } elseif ($settings['dce_form_save_type_post_post']) {
                    $obj_id = $settings['dce_form_save_type_post_post'];
                }
                if (!$obj_id || !$settings['dce_form_save_override']) {
                    // ADD NEW
                    $db_ins['post_status'] = $settings['dce_form_save_type_post_status'];
                    $db_ins['post_type'] = Helper::validate_post_type($settings['dce_form_save_type_post_type']);
                    if ($settings['dce_form_save_parent']) {
                        $db_ins['post_parent'] = $fields['submitted_on_id'];
                    }
                    $obj_id = wp_insert_post($db_ins);
                    $is_update = \false;
                } else {
                    // UPDATE
                    if (!empty($db_ins)) {
                        $db_ins['ID'] = $obj_id;
                        $obj_id = wp_update_post($db_ins);
                    }
                    $is_update = \true;
                }
                $obj = get_post($obj_id);
                $term_id = $settings['dce_form_save_type_post_term'];
                if (!empty($term_id)) {
                    $term = get_term($term_id);
                    $set_terms = wp_set_object_terms($obj_id, $term->slug, $term->taxonomy);
                }
                $taxonomies = Helper::get_taxonomies();
                if (!empty($taxonomies) && !empty($fields)) {
                    $fields_keys = \array_keys($fields);
                    $taxonomies_keys = \array_keys($taxonomies);
                    $taxonomy_fields = \array_intersect($taxonomies_keys, $fields_keys);
                    if (!empty($taxonomy_fields)) {
                        foreach ($taxonomy_fields as $atax) {
                            $term_ids = $fields[$atax];
                            $term_ids = Helper::str_to_array(',', $term_ids);
                            $terms_slug = array();
                            if (!empty($term_ids)) {
                                foreach ($term_ids as $term_id) {
                                    $term = Helper::get_term($term_id, $atax);
                                    if ($term) {
                                        $terms_slug[] = $term->slug;
                                    } else {
                                        // create a new term
                                        $terms_slug[] = $term_id;
                                    }
                                }
                            }
                            if (!empty($terms_slug)) {
                                $set_terms = wp_set_object_terms($obj_id, $terms_slug, $atax);
                            }
                        }
                    }
                }
                break;
            case 'user':
                $settings['dce_form_save_type_user_username'] = sanitize_user(Helper::get_dynamic_value($settings['dce_form_save_type_user_username'], $fields));
                if (!$settings['dce_form_save_type_user_username']) {
                    $settings['dce_form_save_type_user_username'] = 'user_' . \time();
                }
                $settings['dce_form_save_type_user_email'] = Helper::get_dynamic_value($settings['dce_form_save_type_user_email'], $fields);
                $settings['dce_form_save_type_user_pass'] = !empty($settings['dce_form_save_type_user_pass']) ? Helper::get_dynamic_value($settings['dce_form_save_type_user_pass'], $fields) : wp_generate_password();
                $user_email_exist = get_user_by('email', $settings['dce_form_save_type_user_email']);
                $user_login_exist = get_user_by('login', $settings['dce_form_save_type_user_username']);
                $db_ins = array();
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        if (Helper::is_userdata($akey)) {
                            if (empty($db_ins[$akey])) {
                                $db_ins[$akey] = $adata;
                            }
                            unset($fields[$akey]);
                        }
                    }
                }
                // get ID
                $obj_id = get_current_user_id();
                if ($settings['dce_form_save_type_obj_id']) {
                    $obj_id = $this->get_obj_id($settings['dce_form_save_type_obj_id'], 'user', $ajax_handler);
                    if (!$obj_id) {
                        return \false;
                    }
                } elseif ($settings['dce_form_save_type_user_user']) {
                    $obj_id = $settings['dce_form_save_type_user_user'];
                }
                $error_msg = !empty($settings['dce_form_save_type_user_error']) ? $settings['dce_form_save_type_user_error'] : \ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SUBSCRIBER_ALREADY_EXISTS, $settings);
                if (!$obj_id || !$settings['dce_form_save_override']) {
                    if (!get_option('users_can_register')) {
                        $ajax_handler->add_error_message(__('User registration is currently disabled, Please enable it in WordPress Settings - General - Membership.', 'dynamic-content-for-elementor'));
                        return;
                    }
                    if ($user_email_exist || $user_login_exist) {
                        $ajax_handler->add_error_message($error_msg);
                        if ($settings['dce_form_save_type_user_error_stop']) {
                            $ajax_handler->send();
                            die;
                        }
                        return \false;
                    }
                    $db_ins['user_login'] = sanitize_text_field($settings['dce_form_save_type_user_username']);
                    $email = sanitize_email($settings['dce_form_save_type_user_email']);
                    if (!is_email($email)) {
                        $ajax_handler->add_error_message('Email not valid');
                        if ($settings['dce_form_save_type_user_error_stop']) {
                            $ajax_handler->send();
                            die;
                        }
                        return \false;
                    }
                    $db_ins['user_email'] = $email;
                    $db_ins['user_pass'] = sanitize_text_field($settings['dce_form_save_type_user_pass']);
                    $db_ins['role'] = $settings['dce_form_save_type_user_role'];
                    $obj_id = wp_insert_user($db_ins);
                } else {
                    if ($user_email_exist && $user_email_exist->ID != $obj_id || $user_login_exist && $user_login_exist->ID != $obj_id) {
                        $ajax_handler->add_error_message($error_msg);
                        if ($settings['dce_form_save_type_user_error_stop']) {
                            $ajax_handler->send();
                            die;
                        }
                        return \false;
                    }
                    $db_ins['ID'] = $obj_id;
                    $obj_id = wp_update_user($db_ins);
                    $user = get_userdata($obj_id);
                    if ($settings['dce_form_save_type_user_role_mode'] != 'ignore') {
                        $role_mode = $settings['dce_form_save_type_user_role_mode'] . '_role';
                        $user->{$role_mode}($settings['dce_form_save_type_user_role']);
                        // refresh or add role
                    }
                }
                break;
            case 'term':
                $settings['dce_form_save_type_term_name'] = Helper::get_dynamic_value($settings['dce_form_save_type_term_name'], $fields);
                $settings['dce_form_save_type_term_description'] = Helper::get_dynamic_value($settings['dce_form_save_type_term_description'], $fields);
                if ($settings['dce_form_save_type_term_description']) {
                    $db_ins['description'] = $settings['dce_form_save_type_term_description'];
                }
                $db_ins = array();
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        if (!Helper::is_term_meta($akey)) {
                            if (empty($db_ins[$akey])) {
                                $db_ins[$akey] = $adata;
                            }
                            unset($fields[$akey]);
                        }
                    }
                }
                // get ID
                $obj_id = $fields['submitted_on_id'];
                if ($settings['dce_form_save_type_obj_id']) {
                    $obj_id = $this->get_obj_id($settings['dce_form_save_type_obj_id'], 'term', $ajax_handler);
                    if (!$obj_id) {
                        return \false;
                    }
                } elseif ($settings['dce_form_save_type_term_term']) {
                    $obj_id = $settings['dce_form_save_type_term_term'];
                }
                if (!$obj_id || !$settings['dce_form_save_override']) {
                    // ADD NEW
                    $db_ins = array('description' => $settings['dce_form_save_type_term_description']);
                    if (!$settings['dce_form_save_type_term_name']) {
                        $settings['dce_form_save_type_term_name'] = 'Term ' . \time();
                    }
                    $obj_id = wp_insert_term($settings['dce_form_save_type_term_name'], $settings['dce_form_save_type_term_taxonomy'], $db_ins);
                } else {
                    // UPDATE
                    if ($settings['dce_form_save_type_term_name']) {
                        $db_ins['name'] = $settings['dce_form_save_type_term_name'];
                    }
                    if (!empty($db_ins)) {
                        $term = get_term($obj_id);
                        $obj_id = wp_update_term($obj_id, $term->taxonomy, $db_ins);
                    }
                }
                break;
            case 'option':
                $obj_id = -1;
                break;
        }
        if (is_wp_error($obj_id)) {
            $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $settings));
        }
        if ($obj_id) {
            if ($settings['dce_form_save_file']) {
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adatas) {
                        $afield = Helper::get_field($akey, $settings);
                        if ($afield && $afield['field_type'] == 'upload') {
                            $files = Helper::str_to_array(',', $adatas);
                            if (!empty($files)) {
                                foreach ($files as $adata) {
                                    if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                        $filename = Helper::url_to_path($adata);
                                        if (\is_file($filename)) {
                                            // Check the type of file. We'll use this as the 'post_mime_type'.
                                            $filetype = wp_check_filetype(\basename($filename), null);
                                            $fileinfo = \pathinfo($filename);
                                            // Prepare an array of post data for the attachment.
                                            $attachment = array('guid' => $adata, 'post_mime_type' => $filetype['type'], 'post_status' => 'inherit', 'post_title' => $fileinfo['filename'], 'post_parent' => $obj_id);
                                            if ($obj_id <= 0) {
                                                unset($attachment['post_parent']);
                                            }
                                            // Insert the attachment.
                                            $attach_id = wp_insert_attachment($attachment, $filename, $obj_id);
                                            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                                            require_once ABSPATH . 'wp-admin/includes/image.php';
                                            // Generate the metadata for the attachment, and update the database record.
                                            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                                            wp_update_attachment_metadata($attach_id, $attach_data);
                                            if ($afield['allow_multiple_upload']) {
                                                if (\is_array($fields[$akey])) {
                                                    $fields[$akey][] = $attach_id;
                                                } else {
                                                    $fields[$akey] = array($attach_id);
                                                }
                                            } else {
                                                $fields[$akey] = $attach_id;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($fields) && \is_array($fields)) {
                if (!empty($settings['dce_form_save_metas'])) {
                    $settings['dce_form_save_metas'] = \explode(',', \str_replace(' ', '', $settings['dce_form_save_metas']));
                }
                foreach ($fields as $akey => $adata) {
                    if (!empty($settings['dce_form_save_metas']) && !\in_array($akey, $settings['dce_form_save_metas'])) {
                        continue;
                    }
                    if ($settings['dce_form_save_anonymous'] && ($akey == 'ip_address' || $akey == 'referrer' || $akey == 'submitted_by_id')) {
                        continue;
                    }
                    if ($settings['dce_form_save_array']) {
                        $afield = Helper::get_field($akey, $settings);
                        if ($afield && ($afield['field_type'] == 'checkbox' || $afield['field_type'] == 'select' && $afield['allow_multiple'] || $afield['field_type'] == 'upload' && $afield['allow_multiple_upload'])) {
                            $adata = Helper::str_to_array(',', $adata);
                        }
                    }
                    if ($obj_id < 0) {
                        $exist_opt = \false;
                        if (!$settings['dce_form_save_override']) {
                            $exist_opt = get_option($akey);
                        }
                        if ($settings['dce_form_save_override'] || !$exist_opt) {
                            update_option($akey, $adata);
                        }
                    } else {
                        // If you are using 'term' the obj_id is on the key 'term_id'
                        if ('term' == $settings['dce_form_save_type']) {
                            $obj_id = $obj_id['term_id'];
                        }
                        /* allow users to use meta keys names different than field names: */
                        $akey = apply_filters('dynamicooo/form-save/meta-key', $akey, $settings['form_name']);
                        switch ($settings['dce_form_save_type']) {
                            case 'post':
                                update_post_meta($obj_id, $akey, $adata);
                                break;
                            case 'user':
                                update_user_meta($obj_id, $akey, $adata);
                                break;
                            case 'term':
                                update_term_meta($obj_id, $akey, $adata);
                                break;
                        }
                    }
                }
            }
        } else {
            $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $settings));
        }
        if ($settings['dce_form_save_type'] == 'user' && !get_current_user_id() && 'yes' === $settings['dce_form_save_type_user_login']) {
            global $user;
            $credentials = ['user_login' => $db_ins['user_email'], 'user_password' => $db_ins['user_pass']];
            $user = wp_signon($credentials, is_ssl());
            if (is_wp_error($user)) {
                $ajax_handler->add_error_message('Login fail');
                if ($settings['dce_form_save_type_user_error_stop']) {
                    $ajax_handler->send();
                    die;
                }
            }
        }
        if ($settings['dce_form_save_type'] == 'post') {
            do_action('save_post', $obj_id, $obj, $is_update);
        }
        if ($settings['dce_form_save_redirect']) {
            switch ($settings['dce_form_save_type']) {
                case 'post':
                    $redirect_to = get_permalink($obj_id);
                    break;
                case 'user':
                    $author_id = get_the_author_meta('ID');
                    $redirect_to = get_author_posts_url($obj_id ?? $author_id);
                    break;
                case 'term':
                    $redirect_to = get_term_link($obj_id);
                    break;
            }
            $redirect_to = apply_filters('dynamicooo/save/redirect-url', $redirect_to, $obj_id, $settings['form_name']);
            if (!empty($redirect_to) && \filter_var($redirect_to, \FILTER_VALIDATE_URL)) {
                $ajax_handler->add_response_data('redirect_url', $redirect_to);
            }
        }
    }
    protected function get_obj_id($obj_id, $type, $ajax_handler)
    {
        $obj_id = Helper::get_dynamic_value($obj_id);
        if (\is_string($obj_id) && \is_numeric($obj_id)) {
            $obj_id = \intval($obj_id);
        }
        if (!$obj_id) {
            $ajax_handler->add_error_message($type . ' ID not valid');
            return \false;
        }
        switch ($type) {
            case 'post':
                $obj_check = get_post($obj_id);
                break;
            case 'user':
                $obj_check = get_user_by('ID', $obj_id);
                break;
            case 'term':
                $obj_check = get_term($obj_id);
                break;
        }
        if (!$obj_check) {
            $ajax_handler->add_error_message($type . ' not existent');
            return \false;
        }
        return $obj_id;
    }
    public function on_export($element)
    {
        return $element;
    }
}
