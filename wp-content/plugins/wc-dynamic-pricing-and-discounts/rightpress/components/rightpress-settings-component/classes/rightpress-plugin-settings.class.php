<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-settings.class.php';
require_once 'interfaces/rightpress-plugin-settings-interface.php';

/**
 * =================================================================================================================
 * SETTINGS STRUCTURE
 * =================================================================================================================
 *
 * array(
 *
 *     {tab_key} => array(
 *         'title'    => {tab_title},
 *         'children' => array(
 *
 *             {section_key} => array(
 *                 'title'    => {section_title},
 *                 'children' => array(
 *
 *                     {setting_key} => array(
 *                         'type'  => {setting_type},
 *                         'title' => {setting_title},
 *                         ... other setting options ...
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     )
 * )
 *
 * =================================================================================================================
 * ATTRIBUTES
 * =================================================================================================================
 *
 * Attributes for all setting types:
 *  type             required        supported setting types: text, number, decimal, textarea, checkbox, select, grouped_select, multiselect, file, link
 *  title            required
 *  after            optional        text displayed right of the field
 *  default          optional
 *  class            optional        extra css classes for input element
 *  validation       optional        supported validation rules: required, number_min_0, number_natural, number_min_1, number_whole     TODO: need to change from data-rightpress-settings-validation, data-rp-wcdpd-validation
 *  stored           optional        default=true, whether to sanitize and store field value (e.g. link is for display only)
 *  hint             optional
 *  conditions       optional        array defining conditions related to other settings for this setting to be visible/enabled
 *
 * Attributes for text, number, decimal, textarea:
 *  placeholder      optional
 *
 * Attributes for select, grouped_select, multiselect:
 *  options          required
 *
 * Attributes for link:
 *  link_label       required        TODO: maybe use description or something like that?
 *  link_url         required
 *
 * =================================================================================================================
 * FRONTEND VALIDATION
 * =================================================================================================================
 *
 * Format:
 *     'validation' => array(
 *         {validation_rule_method} => {validation_rule_value},
 *         ... other frontend validation rules for this setting ...
 *     ),
 *
 * Supported methods:
 *  is_required         null            whether value is required
 *  is_whole            null            whether numeric value is a whole number
 *  is_natural          null            whether numeric value is a natural number
 *  min                 float|int       minimum value for a numeric setting
 *
 * =================================================================================================================
 * CONDITIONS
 * =================================================================================================================
 *
 * Format:
 *     'conditions' => array(
 *         {parent_setting_key} => array(
 *             {condition_method} => {condition_value},
 *             ... other conditions if child has multiple conditions for the same parent ...
 *         ),
 *         ... other parent settings if child setting depends on multiple parents ...
 *     ),
 *
 * Supported methods:
 *  is_checked     null      whether a checkbox or radio button is checked
 *
 */

    // TODO: Maybe prepare to handle checkbox and radio buttons sets (i.e. multiple checkboxes/radios per field)

/**
 * RightPress Plugin Settings Class
 *
 * @class RightPress_Plugin_Settings
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Plugin_Settings extends RightPress_Settings implements RightPress_Plugin_Settings_Interface
{

    // Define settings structure
    protected $structure    = null;
    protected $options      = array();

    // Keep settings in memory
    protected $settings = array();

    // Check if submitted settings were already processed - wwhen plugin settings are saved for the first time,
    // WordPress calls this method twice and $input is different on a second call
    protected $processed = false;

    // Tab key for import and export section
    protected $import_export_tab_key = null;


    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up settings import and export
        $this->set_up_import_export();

        // Load settings
        $this->load_settings();

        // Register settings capability
        $this->register_capability();

        // Register settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add link to menu
        add_action('admin_menu', array($this, 'add_to_menu'), 12);

        // Enqueue assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'), 20);
    }


    /**
     * =================================================================================================================
     * METHODS FOR EXTERNAL ACCESS
     * =================================================================================================================
     */

    /**
     * Check if specific setting value matches provided value (strict comparison) or equals true
     *
     * @access public
     * @param string $key
     * @param mixed $value_to_check
     * @return bool
     */
    public static function is($key, $value_to_check = null)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Check value and return
        return $instance->setting_value_is($key, $value_to_check);
    }

    /**
     * Get value of a single setting
     *
     * @access public
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Return settings value
        return $instance->get_setting_value($key, $default);
    }

    /**
     * Get all settings in array
     *
     * @access public
     * @return array
     */
    public static function get_all()
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Return settings array
        return $instance->settings;
    }

    /*
     * Update value of a single setting
     *
     * @access public
     * @return bool
     */
    public static function update($key, $value)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // User not allowed to update settings
        if (!current_user_can($instance->get_capability())) {
            return false;
        }

        // Setting not defined in settings structure
        if (!isset($instance->settings[$key])) {
            RightPress_Help::doing_it_wrong((get_called_class() . '::' . __FUNCTION__), 'Setting ' . $key . ' is not defined in settings structure.', '1.0');
            return false;
        }

        // Update setting and return
        $instance->settings[$key] = $value;
        $instance->store();
        return true;
    }

    /**
     * Get options for use in select fields
     *
     * @access public
     * @param string $key
     * @return array
     */
    public static function get_options($key)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Return options
        return $instance->get_setting_options($key);
    }

    /**
     * Get current settings tab
     *
     * @access public
     * @return string
     */
    public static function get_tab()
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Return current tab
        return $instance->get_current_settings_tab();
    }

    /**
     * Get tab title by tab key
     *
     * @access public
     * @param string $key
     * @return string
     */
    public static function get_tab_title($key)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Get settings structure
        $structure = $instance->get_structure();

        // Tab does not exist
        if (empty($structure[$key])) {
            RightPress_Help::doing_it_wrong((get_called_class() . '::' . __FUNCTION__), 'Settings tab ' . $key . ' is not defined in settings structure.', '1.0');
            return false;
        }

        // Return tab title
        return $structure[$key]['title'];
    }

    /**
     * Check if tab has at least one setting
     *
     * @access public
     * @param array $tab
     * @return bool
     */
    final public static function tab_has_settings($tab)
    {

        foreach ($tab['children'] as $section_key => $section) {
            if (self::section_has_settings($section)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if section has at least one setting
     *
     * @access public
     * @param array $section
     * @return bool
     */
    final public static function section_has_settings($section)
    {

        return !empty($section['children']);
    }

    /**
     * Print settings field manually
     *
     * @access public
     * @param string $field_key
     * @return void
     */
    public static function print_settings_field($field_key)
    {

        // Get instance
        $instance = self::get_child_instance(get_called_class());

        // Find field
        foreach ($instance->get_structure() as $tab_key => $tab) {
            foreach ($tab['children'] as $section_key => $section) {
                foreach ($section['children'] as $current_field_key => $field) {
                    if ($current_field_key === $field_key) {

                        // Format method name
                        $method = 'print_field_' . $field['type'];

                        // Print field
                        $instance->$method(array(
                            'field_key' => $field_key,
                            'field'     => $field,
                        ));

                        return;
                    }
                }
            }
        }
    }

    /**
     * Get child instance
     *
     * Note: This is a helper method for local methods, should not be called externally
     *
     * @access private
     * @param string $called_class
     * @return object
     */
    private static function get_child_instance($called_class)
    {

        // Methods calling this method must be called through a child class
        if ($called_class === __CLASS__) {
            RightPress_Help::doing_it_wrong(__METHOD__, 'This method must be called through a child class.', '1.0');
            exit;
        }

        // Return child instance
        return $called_class::get_instance();
    }

    /**
     * =================================================================================================================
     * SETTINGS INITIALIZATON
     * =================================================================================================================
     */

    /**
     * Load settings
     *
     * @access private
     * @return void
     */
    private function load_settings()
    {

        // Load stored settings
        $stored = get_option($this->get_settings_key(), array());

        // Maybe migrate settings from older version
        if (empty($stored) || empty($stored[$this->version])) {
            $stored = $this->migrate_settings($stored);
        }

        // Get settings of current version
        $stored = (is_array($stored) && isset($stored[$this->version])) ? $stored[$this->version] : array();

        // Iterate over field structure and either assign stored value or revert to default value
        foreach ($this->get_structure() as $tab_key => $tab) {
            foreach ($tab['children'] as $section_key => $section) {
                foreach ($section['children'] as $field_key => $field) {

                    // Set value
                    if (isset($stored[$field_key])) {
                        $this->settings[$field_key] = $stored[$field_key];
                    }
                    else {
                        $this->settings[$field_key] = isset($field['default']) ? $field['default'] : null;
                    }

                    // Set options
                    if (!empty($field['options'])) {
                        $this->options[$field_key] = $field['options'];
                    }
                }
            }
        }
    }

    /**
     * Get settings key
     *
     * @access protected
     * @return string
     */
    protected function get_settings_key()
    {

        return $this->get_plugin_private_prefix() . 'settings';
    }

    /**
     * Get settings group key
     *
     * @access protected
     * @param string $tab_key
     * @return string
     */
    protected function get_settings_group_key($tab_key)
    {

        return $this->get_settings_key() . '_group_' . $tab_key;
    }

    /**
     * Get settings page id
     *
     * @access protected
     * @param string $tab_key
     * @return string
     */
    protected function get_settings_page_id($tab_key)
    {

        return str_replace('_', '-', $this->get_plugin_private_prefix()) . 'admin-' . str_replace('_', '-', $tab_key);
    }

    /**
     * Get settings structure
     *
     * @access protected
     * @return array
     */
    protected function get_structure()
    {

        if ($this->structure === null) {

            // Define settings structure
            $this->structure = $this->define_structure();

            // Allow other classes to add settings dynamically
            $this->structure = apply_filters(($this->get_plugin_private_prefix() . 'settings_structure'), $this->structure);
        }

        return $this->structure;
    }

    /**
     * Register settings capability
     *
     * @access protected
     * @return void
     */
    protected function register_capability()
    {

        foreach ($this->get_structure() as $tab_key => $tab) {
            add_filter(('option_page_capability_' . $this->get_settings_key() . '_' . $tab_key), array($this, 'get_capability'));
        }
    }

    /**
     * Check if current request is for a plugin's settings page
     *
     * @access protected
     * @return bool
     */
    protected function is_settings_page()
    {

        return (strpos($_SERVER['REQUEST_URI'], ('page=' . $this->get_settings_key()))) !== false;
    }

    /**
     * Check if specific setting value matches provided value (strict comparison) or equals true
     *
     * @access protected
     * @param string $key
     * @param mixed $value_to_check
     * @return bool
     */
    protected function setting_value_is($key, $value_to_check = null)
    {

        // Get setting value
        $value = $this->get_setting_value($key, false);

        // Value not available
        if ($value === false) {
            return false;
        }

        // Compare as bool
        if ($value_to_check === null) {
            return (bool) $value;
        }

        // Value does not match
        if ($value !== $value_to_check) {
            return false;
        }

        // Value matches
        return true;
    }

    /**
     * Get value of a single setting
     *
     * @access protected
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get_setting_value($key, $default = null)
    {

        // Get setting value
        $value = isset($this->settings[$key]) ? $this->settings[$key] : $default;

        // Allow developers to override value and return it
        return apply_filters(($this->get_settings_key() . '_value'), $value, $key);
    }

    /**
     * Store settings in database
     *
     * @access private
     * @return void
     */
    private function store()
    {

        update_option($this->get_settings_key(), array(
            $this->version => $this->settings
        ));
    }

    /**
     * Get options for use in select fields
     *
     * @access protected
     * @param string $key
     * @return array
     */
    protected function get_setting_options($key)
    {

        // Return options or empty array
        return isset($this->options[$key]) ? $this->options[$key] : array();
    }

    /**
     * Register settings with WordPress
     *
     * @access public
     * @return void
     */
    public function register_settings()
    {

        // Check if current user has administrative capability
        if (!current_user_can($this->get_capability())) {
            return;
        }

        // Iterate over tabs
        foreach ($this->get_structure() as $tab_key => $tab) {

            // Tab has no settings
            if (!self::tab_has_settings($tab)) {
                continue;
            }

            // Register tab
            register_setting(
                $this->get_settings_group_key($tab_key),
                $this->get_settings_key(),
                array(
                    'sanitize_callback' => array($this, 'sanitize_settings'),
                )
            );

            // Iterate over sections
            foreach ($tab['children'] as $section_key => $section) {

                // Section has no settings and empty section should not be displayed
                if (!self::section_has_settings($section) && empty($section['print_empty'])) {
                    continue;
                }

                // Format settings page id
                $settings_page_id = $this->get_settings_page_id($tab_key);

                // Register section
                add_settings_section(
                    $section_key,
                    $section['title'],
                    array($this, 'print_section_info'),
                    $settings_page_id
                );

                // Iterate over fields
                foreach ($section['children'] as $field_key => $field) {

                    // Register field
                    add_settings_field(
                        $this->get_plugin_private_prefix() . $field_key,
                        $field['title'],
                        array($this, 'print_field_' . $field['type']),
                        $settings_page_id,
                        $section_key,
                        array(
                            'field_key' => $field_key,
                            'field'     => $field,
                        )
                    );
                }
            }
        }
    }

    /**
     * Add Settings link to menu
     *
     * @access public
     * @return void
     */
    public function add_to_menu()
    {

        // Register
        add_submenu_page(
            apply_filters(($this->get_plugin_private_prefix() . 'parent_menu_key'), $this->parent_menu_key),
            esc_html__('Settings', 'rightpress'),
            esc_html__('Settings', 'rightpress'),
            $this->get_capability(),
            $this->get_settings_key(),
            array($this, 'print_settings_page')
        );

        // Add menu item
        add_filter(($this->get_plugin_private_prefix() . 'menu_items'), function($items) {
            return array_merge($items, array($this->get_settings_key()));
        }, 200);
    }

    /**
     * Get settings page url
     *
     * TODO: Other plugins that have $parent_menu_key set to something else but URL path, must override this method
     *
     * @access protected
     * @param array $query_vars
     * @return string
     */
    protected function get_settings_page_url($query_vars = array())
    {

        // Get base url
        $url = $this->parent_menu_key;

        // Add query vars
        $url = add_query_arg(array_merge(array('page' => $this->get_settings_key()), $query_vars), $url);

        // Return url
        return $url;
    }

    /**
     * Get notice key
     *
     * @access protected
     * @return string
     */
    protected function get_notice_key()
    {

        return ($this->get_plugin_private_prefix() . 'notice');
    }

    /**
     * Get current settings tab
     *
     * @access protected
     * @return string
     */
    protected function get_current_settings_tab()
    {

        // Get settings structure
        $structure = $this->get_structure();

        // Check if we know tab identifier
        if (isset($_GET['tab']) && isset($structure[$_GET['tab']])) {
            return $_GET['tab'];
        }
        // Get default tab
        else {
            $array_keys = array_keys($structure);
            return array_shift($array_keys);
        }
    }


    /**
     * =================================================================================================================
     * SETTINGS PRINTING
     * =================================================================================================================
     */

    /**
     * Print settings page
     *
     * @access public
     * @return void
     */
    public function print_settings_page()
    {

        // Get current tab
        $current_tab = $this->get_current_settings_tab();

        // Open form container
        echo '<div class="wrap woocommerce"><form method="post" action="options.php" enctype="multipart/form-data">';

        // Print errors
        settings_errors($this->get_notice_key());

        // Get base path
        $base_path = RightPress_Loader::get_component_path('rightpress-settings-component', 'views/plugin/');

        // Print views
        include $base_path . 'header.php';
        include $base_path . 'settings.php';
        include $base_path . 'footer.php';
        include $base_path . 'preloader.php';

        // Allow other classes to print their content
        do_action(($this->get_plugin_private_prefix() . 'after_settings_page_footer'), array('current_tab' => $current_tab));

        // Close form container
        echo '</form></div>';
    }

    /**
     * Enqueue assets
     *
     * @access public
     * @return void
     */
    public function enqueue_assets()
    {

        // Not settings page
        if (!$this->is_settings_page()) {
            return;
        }

        // Get base url
        $base_url = RightPress_Loader::get_component_url('rightpress-settings-component', 'assets/');

        // Get version number
        $version = RightPress_Loader::get_version();

        // Enqueue jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');

        // jQuery UI Tooltip
        wp_enqueue_script('jquery-ui-tooltip');

        // Enqueue scripts
        wp_enqueue_script('rightpress-settings', ($base_url . 'js/settings.js'), array('jquery'), $version);
        wp_enqueue_script('rightpress-plugin-settings', ($base_url . 'js/plugin-settings.js'), array('jquery'), $version);
        wp_enqueue_script('rightpress-plugin-settings-validation', ($base_url . 'js/plugin-settings-validation.js'), array('jquery'), $version);

        // Enqueue styles
        wp_enqueue_style('rightpress-settings-styles', ($base_url . 'css/settings.css'), array(), $version);
        wp_enqueue_style('rightpress-plugin-settings-styles', ($base_url . 'css/plugin-settings.css'), array(), $version);

        // Enqueue Select2 related scripts and styles
        wp_enqueue_script('rightpress-settings-select2-scripts', ($base_url . 'select2/js/select2.full.min.js'), array('jquery'), '4.0.12');
        wp_enqueue_style('rightpress-settings-select2-styles', ($base_url . 'select2/css/select2.min.css'), array(), '4.0.12');

        // Print scripts before WordPress takes care of it automatically (helps load our version of Select2 before any other plugin does it)
        add_action('wp_print_scripts', array($this, 'print_select2'));

        // Define data to be passed to JavaScript
        $data = array(
            'current_tab'   => $this->get_current_settings_tab(),
            'conditions'    => array(),
            'validation'    => array(),
        );

        // Extract data from fields
        foreach ($this->get_structure() as $tab_key => $tab) {
            foreach ($tab['children'] as $section_key => $section) {
                foreach ($section['children'] as $field_key => $field) {

                    // Settings visibility conditions
                    if (!empty($field['conditions'])) {
                        foreach ($field['conditions'] as $parent_field_key => $conditions) {
                            $data['conditions'][($this->get_plugin_private_prefix() . $field_key)][($this->get_plugin_private_prefix() . $parent_field_key)] = $conditions;
                        }
                    }

                    // Frontend validation rules
                    if (!empty($field['validation'])) {
                        $data['validation'][($this->get_plugin_private_prefix() . $field_key)] = $field['validation'];
                    }
                }
            }
        }

        // Pass data to JavaScript
        wp_localize_script('rightpress-plugin-settings', 'rightpress_plugin_settings', $data);

        // Pass labels to JavaScript
        wp_localize_script('rightpress-plugin-settings', 'rightpress_plugin_settings_labels', array(
            'select2_tags_placeholder'   => esc_html__('Add values', 'rightpress'),
            'select2_tags_no_results'    => esc_html__('Start typing...', 'rightpress'),
        ));

        // Pass data to validation script
        wp_localize_script('rightpress-plugin-settings-validation', 'rightpress_plugin_settings_validation', array(
            'error_messages' => array(
                'generic_error' => esc_html__('Error: Please fix this element.', 'rightpress'),
                'is_required'   => esc_html__('Value is required.', 'rightpress'),
                'is_whole'      => esc_html__('Value must be a whole number.', 'rightpress'),
                'is_natural'    => esc_html__('Value must be positive.', 'rightpress'),
                'min'           => esc_html__('Value must be greater than or equal to {{value}}.', 'rightpress'),
            ),
        ));
    }

    /**
     * Print Select2 scripts
     *
     * Note: We do this to avoid conflicts with other versions of Select2 loaded on the same page
     *
     * @access public
     * @return void
     */
    public function print_select2()
    {

        remove_action('wp_print_scripts', array($this, 'print_select2'));
        wp_print_scripts('rightpress-settings-select2-scripts');
    }

    /**
     * Print section info
     *
     * @access public
     * @param array $section
     * @return void
     */
    public function print_section_info($section)
    {

        foreach ($this->get_structure() as $tab_key => $tab) {
            if (!empty($tab['children'][$section['id']]['info'])) {
                echo '<p>' . $tab['children'][$section['id']]['info'] . '</p>';
            }
        }
    }

    /**
     * Print text field
     *
     * @access public
     * @param array $args
     * @param string $field_type
     * @return void
     */
    public function print_field_text($args = array(), $field_type = 'text')
    {

        // Print field
        RightPress_Forms::$field_type(array_merge($this->prepare_field_config($args), array(
            'value' => htmlspecialchars($this->get_setting_value($args['field_key'])),
        )));

        // Print hint
        $this->print_hint($args);
    }

    /**
     * Print number field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_number($args = array())
    {

        self::print_field_text($args, 'number');
    }

    /**
     * Print decimal field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_decimal($args = array())
    {

        self::print_field_text($args, 'decimal');
    }

    /**
     * Print text area field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_textarea($args = array())
    {

        // Print field
        RightPress_Forms::textarea(array_merge($this->prepare_field_config($args), array(
            'value' => htmlspecialchars($this->get_setting_value($args['field_key'])),
        )));

        // Print hint
        $this->print_hint($args);
    }

    /**
     * Print checkbox field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_checkbox($args = array())
    {

        // Print field
        RightPress_Forms::checkbox(array_merge($this->prepare_field_config($args), array(
            'checked' => (bool) $this->get_setting_value($args['field_key']),
        )));

        // Print hint
        $this->print_hint($args);
    }

    /**
     * Print select field
     *
     * @access public
     * @param array $args
     * @param bool $is_multiselect
     * @param bool $is_grouped
     * @param bool $is_tags
     * @return void
     */
    public function print_field_select($args = array(), $is_multiselect = false, $is_grouped = false, $is_tags = false)
    {

        // Get options
        $options = $this->get_setting_options($args['field_key']);

        // Get value
        $value = $this->get_setting_value($args['field_key']);

        // Set options to value if no options are set and tags are used
        if ($is_tags && empty($options)) {
            $options = $value;
        }

        // Print field
        RightPress_Forms::select(array_merge($this->prepare_field_config($args, '', $is_multiselect), array(
            'options'               => $options,
            'value'                 => $value,
            'data-select-2-tags'    => true,
        )), $is_multiselect, $is_grouped);

        // Print hint
        $this->print_hint($args);
    }

    /**
     * Print grouped select field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_grouped_select($args = array())
    {

        self::print_field_select($args, false, true);
    }

    /**
     * Print multiselect field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_multiselect($args = array())
    {

        self::print_field_select($args, true);
    }

    /**
     * Print tags field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_tags($args = array())
    {

        self::print_field_select($args, true, false, true);
    }

    /**
     * Print file field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_file($args = array())
    {

        self::print_field_text($args, 'file');
    }

    /**
     * Print link field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function print_field_link($args = array())
    {

        // Get properties
        $label = !empty($args['field']['link_label']) ? $args['field']['link_label'] : $args['field']['link_url'];

        // Print link
        echo '<a href="' . $args['field']['link_url'] . '">' . $label . '</a>';
    }

    /**
     * Print hint
     *
     * @access protected
     * @param array $args
     * @return void
     */
    protected function print_hint($args)
    {

        if (!empty($args['field']['hint'])) {
            echo '<div class="rightpress-plugin-settings-hint">' . $args['field']['hint'] . '</div>';
        }
    }

    /**
     * Prepare field config
     *
     * TODO: When adapting this to other plugins, need to check for any Javascript logic that was based on field's id, name or class as these may have changed
     *
     * @access protected
     * @param array $args
     * @param string $custom_class
     * @param bool $is_multiple
     * @return array
     */
    protected function prepare_field_config($args, $custom_class = '', $is_multiple = false)
    {

        // Reference field data
        $field_key = $args['field_key'];
        $field = $args['field'];

        // Prepare field config
        $config = array(
            'title'                         => $field['title'],
            'id'                            => $this->get_plugin_private_prefix() . $field_key,
            'name'                          => $this->get_settings_key() . '[' . $this->get_plugin_private_prefix() . $field_key . ']' . ($is_multiple ? '[]' : ''),
            'class'                         => str_replace('_', '-', $this->get_settings_key()) . '-field rightpress-plugin-settings-field' . (!empty($custom_class) ? (' ' . $custom_class) : '') . (!empty($field['class']) ? (' ' . $field['class']) : ''),
            'required'                      => (!empty($field['validation']) && in_array('required', $field['validation'], true)) ? 'required' : null,
            'disabled'                      => !empty($field['disabled']) ? 'disabled' : null,
            'placeholder'                   => (isset($field['placeholder']) && !RightPress_Help::is_empty($field['placeholder'])) ? $field['placeholder'] : null,
        );

        // Content before field
        if (!empty($args['field']['before'])) {
            $config['before'] = ' ' . $args['field']['before'];
        }

        // Content after field
        if (!empty($args['field']['after'])) {
            $config['after'] = ' ' . $args['field']['after'];
        }

        // Flag fields for visibility control
        if (!empty($args['field']['conditions'])) {
            $config['class'] .= ' rightpress-plugin-settings-has-conditions';
        }

        // Flag fields for frontend validation
        // TODO: We should also do corresponding validation in the backend! Right now we don't do that except for 'required'
        if (!empty($args['field']['validation'])) {
            $config['class'] .= ' rightpress-plugin-settings-has-validation';
        }

        // Return field config
        return $config;
    }


    /**
     * =================================================================================================================
     * SETTINGS SANITIZATION
     * =================================================================================================================
     */

    /**
     * Sanitize submitted settings
     *
     * @access public
     * @param array $input
     * @return array
     */
    public function sanitize_settings($input)
    {

        // Get settings structure
        $structure = $this->get_structure();

        // Set prefix key
        $field_key_prefix = !$this->processed ? $this->get_plugin_private_prefix() : '';

        // Second call - fix input
        if ($this->processed) {
            $input = $input[$this->version];
        }
        // First call - use serialized input data if available (prevents max input vars problem)
        else if ($serialized_settings = $this->get_posted_serialized_settings()) {
            $input = $serialized_settings;
        }

        // Set output to current settings array first
        $output = $this->settings;

        // Store errors
// TODO: Do we really need WP_Error here?
        $errors = new WP_Error();

        // Check if request came from a correct page
        if (!empty($_POST['current_tab']) && isset($structure[$_POST['current_tab']])) {

            // Reference current tab
            $current_tab = $_POST['current_tab'];

            // Iterate over fields and validate new values
            foreach ($structure[$current_tab]['children'] as $section_key => $section) {
                foreach ($section['children'] as $field_key => $field) {

                    // Skip fields for which values are not stored
                    if (isset($field['stored']) && $field['stored'] === false) {
                        continue;
                    }

                    // Prefix field key
                    $prefixed_key = $field_key_prefix . $field_key;

                    // Attempt to sanitize settings value
                    try {

                        // Allow plugins to do custom sanitization
                        if ($sanitized = $this->sanitize_custom($input, $field_key, $prefixed_key, $output[$field_key], $output, $field)) {

                            // Set sanitized value
                            $output[$field_key] = $sanitized;
                        }
                        // Proceed with default sanitization
                        else {

                            // Format method name
                            $method = 'sanitize_field_' . $field['type'];

                            // Sanitize field value
                            $output[$field_key] = $this->$method($input, $prefixed_key, $output[$field_key], $field);
                        }
                    }
                    // Value validation or sanitization failed, add error
                    catch (RightPress_Settings_Exception $e) {
                        $errors->add($e->get_error_code(), $e->getMessage());
                    }
                }
            }

            // Allow plugins to sanitize the whole settings set
            $output = $this->post_sanitization($output, $input, $current_tab);
        }
        // Request came from unknown page
        else {
            $errors->add('rightpress_plugin_settings_sanitization_incorrect_page', esc_html__('Unable to validate settings.', 'rightpress'));
        }

        // Display errors or success notice
        if (!$this->processed) {

            // Display errors
            if ($error_messages = $errors->get_error_messages()) {

                foreach ($error_messages as $message) {

                    add_settings_error(
                        $this->get_notice_key(),
                        $this->get_settings_key() . '_sanitization_failed', // TODO: We should append error codes or something like that since multiple errors would result in elements with identical ids
                        $message
                    );
                }
            }
            // Display success notice
            else {

                add_settings_error(
                    $this->get_notice_key(),
                    $this->get_settings_key() . '_updated',
                    esc_html__('Settings updated.', 'rightpress'),
                    'updated'
                );
            }
        }

        // Set processed flag
        $this->processed = true;

        // Return sanitized settings array
        // TODO: Shouldn't we skip storing options if we have errors?
        return array($this->version => $output);
    }

    /**
     * Get posted serialized settings
     *
     * TODO: Test this functionality when adapting to WCDPD (it wasn't tested when it was first moved to RightPress_Plugin_Settings)
     *
     * @access private
     * @return array|bool
     */
    private function get_posted_serialized_settings()
    {

        $unserialized_settings = array();

        // Check if serialized values were posted
        if (!empty($_POST[($this->get_settings_key() . '_serialized')])) {

            // Explode settings
            $exploded_settings = explode('&', stripslashes($_POST[($this->get_settings_key() . '_serialized')]));

            // Iterate over exploded settings
            foreach ($exploded_settings as $setting) {

                // Parse setting
                $parsed_setting = array();
                parse_str($setting, $parsed_setting);

                // Merge with main array
                if (!empty($parsed_setting[$this->get_settings_key()]) && is_array($parsed_setting[$this->get_settings_key()])) {
                    $unserialized_settings = RightPress_Help::array_merge_recursive_for_indexed_lists($unserialized_settings, $parsed_setting[$this->get_settings_key()]);
                }
            }
        }

        // Return unserialized settings array or false if serialized settings are not available
        return $unserialized_settings || false;
    }

    /**
     * Custom sanitization handler to be overriden by plugins
     *
     * Boolean false return value is reserved to indicate that value was not sanitized
     *
     * TODO: Find some other way to indicate that value was not sanitized instead of using false (false could then be used as actual value)
     *
     * @access protected
     * @param array $input
     * @param string $field_key
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $output
     * @param array $field
     * @return mixed
     */
    protected function sanitize_custom($input, $field_key, $prefixed_key, $current_value, $output, $field)
    {

        return false;
    }

    /**
     * Allow plugins to sanitize the whole settings set
     *
     * @access protected
     * @param array $output
     * @param array $input
     * @param string $current_tab
     * @return array
     */
    protected function post_sanitization($output, $input, $current_tab)
    {

        return $output;
    }

    /**
     * Sanitize text field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return string
     */
    protected function sanitize_field_text($input, $prefixed_key, $current_value, $field)
    {

        // TODO: WCDPD needs special handling here
        // TODO: Shouldn't we reset value to empty string if it's not set in $input instead of preserving current value?
        return isset($input[$prefixed_key]) ? esc_attr(trim($input[$prefixed_key])) : $current_value;
    }

    /**
     * Sanitize number field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return int
     */
    protected function sanitize_field_number($input, $prefixed_key, $current_value, $field)
    {

        return (isset($input[$prefixed_key]) && is_numeric($input[$prefixed_key])) ? (int) esc_attr(trim($input[$prefixed_key])) : '';
    }

    /**
     * Sanitize decimal field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return float
     */
    protected function sanitize_field_decimal($input, $prefixed_key, $current_value, $field)
    {

        return isset($input[$prefixed_key]) && is_numeric($input[$prefixed_key]) ? (float) esc_attr(trim($input[$prefixed_key])) : '';
    }

    /**
     * Sanitize text area field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return string
     */
    protected function sanitize_field_textarea($input, $prefixed_key, $current_value, $field)
    {

        return $this->sanitize_field_text($input, $prefixed_key, $current_value, $field);
    }

    /**
     * Sanitize checkbox field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return string
     */
    protected function sanitize_field_checkbox($input, $prefixed_key, $current_value, $field)
    {

        return empty($input[$prefixed_key]) ? '0' : '1';
    }

    /**
     * Sanitize select field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return string
     */
    protected function sanitize_field_select($input, $prefixed_key, $current_value, $field)
    {

        // TODO: Shouldn't we reset value if it's not set in $input instead of preserving current value?
        return (isset($input[$prefixed_key]) && isset($field['options'][$input[$prefixed_key]])) ? $input[$prefixed_key] : $current_value;
    }

    /**
     * Sanitize grouped select field
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return string
     */
    protected function sanitize_field_grouped_select($input, $prefixed_key, $current_value, $field)
    {

        // TODO: Shouldn't we reset value if it's not set in $input instead of preserving current value?
        $value = $current_value;

        if (isset($input[$prefixed_key])) {
            foreach ($field['options'] as $option_group) {
                if (isset($option_group['options'][$input[$prefixed_key]])) {
                    $value = $input[$prefixed_key];
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Sanitize multiselect field
     *
     * TODO: Update this to use predefined options, previously this was used for tags before creating separate methods for tags
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return array
     */
    protected function sanitize_field_multiselect($input, $prefixed_key, $current_value, $field)
    {

        $output = array();

        if (!empty($input[$prefixed_key]) && is_array($input[$prefixed_key])) {
            foreach ($input[$prefixed_key] as $multiselect_value) {
                $sanitized_value = sanitize_key($multiselect_value);
                $output[$sanitized_value] = $sanitized_value;
            }
        }

        return array_unique($output);
    }

    /**
     * Sanitize tags field
     *
     * Note: this is designed to work with user-entered "tags" with no predefined options list
     *
     * @access protected
     * @param array $input
     * @param string $prefixed_key
     * @param mixed $current_value
     * @param array $field
     * @return array
     */
    protected function sanitize_field_tags($input, $prefixed_key, $current_value, $field)
    {

        $output = array();

        if (!empty($input[$prefixed_key]) && is_array($input[$prefixed_key])) {
            foreach ($input[$prefixed_key] as $tag_value) {
                $sanitized_value = sanitize_key($tag_value);
                $output[$sanitized_value] = $sanitized_value;
            }
        }

        return array_unique($output);
    }

    /**
     * Sanitize file field
     *
     * @access protected
     * @param mixed $value  // TODO: What value this really is?
     * @return mixed
     */
    protected function sanitize_field_file($value)
    {

        // TODO: How do we handle this? Maybe we should skip import file completely from validation since it's handled elsewhere?
        return $value;
    }


    /**
     * =================================================================================================================
     * SETTINGS IMPORT & EXPORT - TODO: review and test all code, relatively little testing went into this
     * =================================================================================================================
     */

    /**
     * Set up settings import and export
     *
     * @access protected
     * @return void
     */
    protected function set_up_import_export()
    {

        // Plugin does not support settings import and export
        if (!$this->import_export_tab_key) {
            return;
        }

        // User not authorized
        if (!current_user_can($this->get_capability())) {
            return;
        }

        // Add import and export options to settings structure
        add_filter(($this->get_plugin_private_prefix() . 'settings_structure'), array($this, 'add_import_export_settings'), 99);

        // Export request
        // TODO: changed from prefix_export_settings to prefix_settings_export (important when adapting for us in WCDPD)
        if (!empty($_REQUEST[($this->get_settings_key() . '_export')])) {
            add_action('wp_loaded', array($this, 'export_settings'));
        }

        // Import request
        if (!empty($_FILES[$this->get_settings_key()]['name'][($this->get_settings_key() . '_import')])) {
            add_action('wp_loaded', array($this, 'import_settings'));
        }

        // Print settings import notice
        if (isset($_REQUEST[($this->get_settings_key() . '_imported')])) {
            add_action('admin_notices', array($this, 'print_settings_import_notice'));
        }
    }

    /**
     * Add import and export options to settings structure
     *
     * @access public
     * @param array $structure
     * @return array
     */
    public function add_import_export_settings($structure)
    {

        // Check if tab is present
        if ($this->import_export_tab_key && isset($structure[$this->import_export_tab_key])) {

            // Add import and export section with settings
            $structure[$this->import_export_tab_key]['children']['import_export'] = array(

                'title' => esc_html__('Import & Export', 'rightpress'),

                'children' => array(

                    'settings_import' => array(
                        'type'      => 'file',
                        'title'     => esc_html__('Import settings', 'rightpress'),
                        'stored'    => false,
                        'hint'      => esc_html__('Warning! Importing settings will irrecoverably overwrite your existing settings.', 'rightpress'),
                    ),

                    'settings_export' => array(
                        'type'          => 'link',
                        'title'         => esc_html__('Export settings', 'rightpress'),
                        'link_label'    => esc_html__('Click here to export', 'rightpress'),
                        'link_url'      => admin_url('?' . $this->get_settings_key() . '_export=1'),
                        'stored'        => false,
                    ),
                ),
            );
        }

        return $structure;
    }

    /**
     * Export settings to file
     *
     * @access public
     * @return void
     */
    public function export_settings()
    {

        // Get settings
        $settings = get_option($this->get_settings_key(), array());

        // Format export data
        $data = array(
            'settings'      => $settings,
            'plugin_key'    => $this->get_settings_key(),
            'timestamp'     => time(),
            'checksum'      => RightPress_Help::get_hash(false, $settings),
        );

        // Send headers
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename="' . $this->get_settings_key() . '.json"');

        // Output content and exit
        echo json_encode($data);
        exit;
    }

    /**
     * Import settings from file
     *
     * @access public
     * @return void
     */
    public function import_settings()
    {

        try {

            // Check if file was uploaded correctly
            if ($_FILES[$this->get_settings_key()]['error'][($this->get_settings_key() . '_import')] !== UPLOAD_ERR_OK || !is_uploaded_file($_FILES[$this->get_settings_key()]['tmp_name'][($this->get_settings_key() . '_import')])) {
                throw new RightPress_Settings_Exception('rightpress_plugin_settings_import_upload_error', '');
            }

            // Get file contents
            $contents = file_get_contents($_FILES[$this->get_settings_key()]['tmp_name'][($this->get_settings_key() . '_import')]);

            // Contents empty
            if (empty($contents)) {
                throw new RightPress_Settings_Exception('rightpress_plugin_settings_import_invalid_file', '');
            }

            // Decode data
            $data = json_decode($contents, true);

            // Check if required properties are set
            // Note: added legacy handling for older versions of WCDPD which did not add plugin_key option to export file
            if (!isset($data['settings']) || empty($data['timestamp']) || empty($data['checksum']) || (empty($data['plugin_key']) && $this->get_settings_key() !== 'rp_wcdpd_settings')) {
                throw new RightPress_Settings_Exception('rightpress_plugin_settings_import_invalid_file', '');
            }

            // Check plugin key
            if (!empty($data['plugin_key']) && $data['plugin_key'] !== $this->get_settings_key()) {
                throw new RightPress_Settings_Exception('rightpress_plugin_settings_import_wrong_plugin', '');
            }

            // Check data integrity
            if ($data['checksum'] !== RightPress_Help::get_hash(false, $data['settings'])) {
                throw new RightPress_Settings_Exception('rightpress_plugin_settings_import_invalid_file', '');
            }

            // Update settings entry in the database
            update_option($this->get_settings_key(), $data['settings']);

            // Redirect away so that regular settings save handler does not overwrite these settings
            wp_redirect($this->get_settings_page_url(array('tab' => $this->import_export_tab_key, ($this->get_settings_key() . '_imported') => 1)));
            exit;
        }
        catch (Exception $e) {

            // Get error code
            $error_code = is_a($e, 'RightPress_Settings_Exception') ? $e->get_error_code() : 'rightpress_plugin_settings_import_upload_error';

            // Redirect to print error notice by error code
            wp_redirect($this->get_settings_page_url(array('tab' => $this->import_export_tab_key, ($this->get_settings_key() . '_imported') => $error_code)));
            exit;
        }
    }

    /**
     * Print settings import notice
     *
     * @access public
     * @return void
     */
    public function print_settings_import_notice()
    {

        // Success notice
        if ($_REQUEST[($this->get_settings_key() . '_imported')] === '1') {

            add_settings_error(
                $this->get_notice_key(),
                $this->get_settings_key() . '_updated',
                esc_html__('Settings were successfully imported.', 'rightpress'),
                'updated'
            );
        }
        // Error notice
        else {

            // Get error message
            switch ($_REQUEST[($this->get_settings_key() . '_imported')]) {

                // Invalid file
                case 'rightpress_plugin_settings_import_invalid_file':
                    $message = esc_html__('Error: Uploaded settings file is invalid. It might be corrupted or truncated.', 'rightpress');
                    break;

                // Invalid plugin
                case 'rightpress_plugin_settings_import_wrong_plugin':
                    $message = esc_html__('Error: Uploaded file contains settings for a different plugin. Please check your file and try again.', 'rightpress');
                    break;

                // Generic error
                default:
                    $message = esc_html__('Error: There was an error processing uploaded settings file. Please try again.', 'rightpress');
                    break;
            }

            // Add error
            add_settings_error(
                $this->get_notice_key(),
                $this->get_settings_key() . '_updated',
                $message
            );
        }
    }





}
