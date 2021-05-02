<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://plugins.db-dzine.com
 * @since      1.0.0
 */

class WordPress_GDPR
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     *
     * @var WordPress_GDPR_Loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     *
     * @var string The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     *
     * @var string The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct($version)
    {
        $this->plugin_name = 'wordpress-gdpr';
        $this->version = $version;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - WordPress_GDPR_Loader. Orchestrates the hooks of the plugin.
     * - WordPress_GDPR_i18n. Defines internationalization functionality.
     * - WordPress_GDPR_Admin. Defines all hooks for the admin area.
     * - WordPress_GDPR_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'includes/class-wordpress-gdpr-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'includes/class-wordpress-gdpr-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-consent-log.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-data-export.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-data-delete.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-data-breach.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-data-retention.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-install-pages.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-migrate-services.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-policy-update.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-requests.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-term-order.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-users.php';
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wordpress-gdpr-services.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-cookie-popup.php';
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-cookie-services-management.php';
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-forms.php';
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-integrations.php';
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-public.php';
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wordpress-gdpr-privacy-settings.php';
        

        $this->loader = new WordPress_GDPR_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the WordPress_GDPR_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function set_locale()
    {
        $plugin_i18n = new WordPress_GDPR_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_admin_hooks()
    {
        
        // Admin Interface
        $this->plugin_admin = new WordPress_GDPR_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->plugin_admin, 'init', 1);
        $this->loader->add_action('plugins_loaded', $this->plugin_admin, 'load_extensions');
        $this->loader->add_filter('custom_menu_order', $this->plugin_admin, 'reorder_menu_items');
        $this->loader->add_action('parent_file', $this->plugin_admin, 'menu_highlight');

        $this->requests = new WordPress_GDPR_Requests($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->requests, 'init', 10);
        $this->loader->add_action('add_meta_boxes', $this->requests, 'add_custom_metaboxes', 10, 2);
        $this->loader->add_action('save_post', $this->requests, 'save_custom_metaboxes', 1, 2);
        $this->loader->add_action('init', $this->requests, 'check_action', 20);

        $this->data_export = new WordPress_GDPR_Data_Export($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->data_export, 'init', 10);
        $this->loader->add_action('init', $this->data_export, 'check_action', 20);

        $this->data_delete = new WordPress_GDPR_Data_Delete($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->data_delete, 'init', 10);
        $this->loader->add_action('init', $this->data_delete, 'check_action', 20);

        $this->data_breach = new WordPress_GDPR_Data_Breach($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->data_breach, 'init', 10);
        $this->loader->add_action('init', $this->data_breach, 'check_action', 20);

        $this->policy_update = new WordPress_GDPR_Policy_Update($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->policy_update, 'init', 10);
        $this->loader->add_action('init', $this->policy_update, 'check_action', 20);

        $this->install_pages = new WordPress_GDPR_Install_Pages($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->install_pages, 'init', 10);
        $this->loader->add_action('init', $this->install_pages, 'check_action', 20);
        $this->loader->add_action('publish_post', $this->install_pages, 'delete_transient', 20);

        $this->data_retention = new WordPress_GDPR_Data_Retention($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->data_retention, 'init', 10);
        $this->loader->add_action('init', $this->data_retention, 'maybe_delete_old_users', 15);
        $this->loader->add_action('init', $this->data_retention, 'check_action', 20);
        $this->loader->add_action('wp_login', $this->data_retention, 'update_last_logged_in', 20);

        $this->consent_log = new WordPress_GDPR_Consent_Log($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wordpress_gdpr_allow_cookies', $this->consent_log, 'update_consent_log', 10);
        $this->loader->add_action('wordpress_gdpr_decline_cookies', $this->consent_log, 'update_consent_log', 10);
        $this->loader->add_action('wordpress_gdpr_update_cookie', $this->consent_log, 'update_consent_log', 10);

        // Cookies Post Type
        $this->services_post_type = new WordPress_GDPR_Services($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->services_post_type, 'init', 20);
        $this->loader->add_filter( 'manage_gdpr_service_posts_columns', $this->services_post_type, 'columns_head');
        $this->loader->add_action( 'manage_gdpr_service_posts_custom_column', $this->services_post_type, 'columns_content', 10, 1);
        $this->loader->add_action( 'add_meta_boxes', $this->services_post_type, 'add_custom_metaboxes', 40, 2);
        $this->loader->add_action( 'save_post', $this->services_post_type, 'save_custom_metaboxes', 1, 2);
        $this->loader->add_action( 'admin_menu', $this->services_post_type, 'add_taxonomy_submenu', 1, 30);

        $this->users = new WordPress_GDPR_Users($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->users, 'init', 10);
        // $this->loader->add_action('admin_menu', $this->users, 'add_users_menu', 1, 40);

        $this->migrate_services = new WordPress_GDPR_Migrate_Services($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->migrate_services, 'init', 30);
        $this->loader->add_action('init', $this->migrate_services, 'check_action', 40);

        $this->term_order = new WordPress_GDPR_Term_Order($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->term_order, 'init', 99);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_public_hooks()
    {
        // Public
        $this->plugin_public = new WordPress_GDPR_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('get_footer', $this->plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $this->plugin_public, 'init', 10);
        add_shortcode( 'wordpress_gdpr_privacy_center', array($this->plugin_public, 'get_privacy_center'));

        // Cookie Popup
        $this->cookie_popup = new WordPress_GDPR_Cookie_Popup($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->cookie_popup, 'init', 10);
        $this->loader->add_action('wp_footer', $this->cookie_popup, 'add_popup', 10);

        // Cookie / Service Management
        $this->cookie_service_management = new WordPress_GDPR_Cookie_Services_Management($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->cookie_service_management, 'init', 10);
        $this->loader->add_action('wp_ajax_check_privacy_setting', $this->cookie_service_management, 'check_privacy_setting', 10);
        $this->loader->add_action('wp_ajax_nopriv_check_privacy_setting', $this->cookie_service_management, 'check_privacy_setting', 10);
        $this->loader->add_action('wp_ajax_check_privacy_settings', $this->cookie_service_management, 'check_privacy_settings', 10);
        $this->loader->add_action('wp_ajax_nopriv_check_privacy_settings', $this->cookie_service_management, 'check_privacy_settings', 10);
        $this->loader->add_action('wp_ajax_update_privacy_setting', $this->cookie_service_management, 'update_privacy_setting', 10);
        $this->loader->add_action('wp_ajax_nopriv_update_privacy_setting', $this->cookie_service_management, 'update_privacy_setting', 10);
        $this->loader->add_action('wp_ajax_wordpress_gdpr_allow_cookies', $this->cookie_service_management, 'allow_cookies', 10);
        $this->loader->add_action('wp_ajax_nopriv_wordpress_gdpr_allow_cookies', $this->cookie_service_management, 'allow_cookies', 10);
        $this->loader->add_action('wp_ajax_wordpress_gdpr_decline_cookies', $this->cookie_service_management, 'decline_cookies', 10);
        $this->loader->add_action('wp_ajax_nopriv_wordpress_gdpr_decline_cookies', $this->cookie_service_management, 'decline_cookies', 10);
        $this->loader->add_action('wp_ajax_wordpress_gdpr_update_privacy_policy_terms', $this->cookie_service_management, 'update_privacy_policy_term', 10);
        $this->loader->add_action('wp_ajax_nopriv_wordpress_gdpr_update_privacy_policy_terms', $this->cookie_service_management, 'update_privacy_policy_term', 10);
        
        
        // Privacy Settings
        $this->privacy_settings = new WordPress_GDPR_Privacy_Settings($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->privacy_settings, 'init', 10);
        $this->loader->add_action('wp_footer', $this->privacy_settings, 'get_privacy_settings_popup', 10);
        $this->loader->add_action('wp_footer', $this->privacy_settings, 'get_privacy_settings_trigger', 10);
        add_shortcode( 'wordpress_gdpr_privacy_policy_accept', array($this->privacy_settings, 'get_privacy_policy_accept'));
        add_shortcode( 'wordpress_gdpr_terms_conditions_accept', array($this->privacy_settings, 'get_terms_conditions_accept'));
        add_shortcode( 'wordpress_gdpr_privacy_settings', array($this->privacy_settings, 'get_privacy_settings_shortcode'));

        // Forms
        $this->forms = new WordPress_GDPR_Forms($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->forms, 'init', 10);
        add_shortcode( 'wordpress_gdpr_forget_me', array($this->forms, 'get_forget_me_form'));
        add_shortcode( 'wordpress_gdpr_contact_dpo', array($this->forms, 'get_contact_dpo_form'));
        add_shortcode( 'wordpress_gdpr_request_data', array($this->forms, 'get_request_data_form'));
        add_shortcode( 'wordpress_gdpr_data_rectification', array($this->forms, 'get_data_rectification_form'));
        
        // Integrations
        $this->integrations = new WordPress_GDPR_Integrations($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->integrations, 'init', 100);

        // WooCommerce
        $this->loader->add_action( 'woocommerce_checkout_after_terms_and_conditions',$this->integrations, 'add_privacy_policy_checkbox_to_checkout');
        $this->loader->add_action( 'woocommerce_register_form',$this->integrations, 'add_privacy_policy_checkbox_to_registration');
        $this->loader->add_action( 'woocommerce_after_checkout_validation',$this->integrations, 'validate_privacy_policy_checkbox', 10, 2);
        $this->loader->add_filter( 'woocommerce_account_menu_items',$this->integrations, 'add_privacy_center_to_my_account_page');
        $this->loader->add_filter( 'woocommerce_product_review_comment_form_args', $this->integrations, 'add_privacy_policy_checkbox_to_review_form');
        $this->loader->add_filter( 'option_woocommerce_enable_guest_checkout', $this->integrations, 'maybe_disable_woocommerce_guest_checkout', 10, 2);

        // Comments
        $this->loader->add_filter( 'comment_form_submit_field', $this->integrations, 'add_privacy_policy_checkbox_to_comment_form', 10, 1);
        $this->loader->add_filter( 'option_comment_registration', $this->integrations, 'maybe_override_comment_registration', 10, 2);
        $this->loader->add_action( 'bp_before_registration_submit_buttons', $this->integrations, 'add_privacy_policy_checkbox_to_buddypress_registration');
        $this->loader->add_action( 'wpcf7_before_send_mail', $this->integrations, 'flamingo_save_data_check', 10, 2);
        $this->loader->add_filter( 'mailster_form_fields', $this->integrations, 'add_privacy_policy_checkbox_to_mailster_registration', 10, 3);    
        $this->loader->add_filter( 'pys_disable_by_gdpr', $this->integrations, 'maybe_disable_pixelyoursite', 10);
        $this->loader->add_filter( 'pll_get_post_types', $this->integrations, 'add_cpt_to_pll', 10, 2);
        $this->loader->add_filter( 'pll_get_taxonomies', $this->integrations, 'add_tax_to_pll', 10, 2);
        $this->loader->add_filter( 'gform_ip_address', $this->integrations, 'remove_gform_ip_saving', 10, 1);

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     *
     * @return string The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     *
     * @return WordPress_GDPR_Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     *
     * @return string The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Get Options
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   mixed                         $option The option key
     * @return  mixed                                 The option value
     */
    protected function get_option($option)
    {
        if(!isset($this->options)) {
            return false;
        }

        if (!is_array($this->options)) {
            return false;
        }

        if (!array_key_exists($option, $this->options)) {
            return false;
        }

        if(function_exists('pll__')) {
            $this->options[$option] = pll__($this->options[$option]);
        }

        return $this->options[$option];
    }
}
