<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RightPress Automatic Updates Class
 *
 * @class RightPress_Updates_7119279
 * @author RightPress
 */
if (!class_exists('RightPress_Updates_7119279')) {

final class RightPress_Updates_7119279
{

    private static $envato_id = 7119279;

    /***************************************************************************
     * WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING
     *
     *       DO NOT CHANGE ANYTHING ABOVE THIS POINT AFTER INITIAL SETUP
     *
     * WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING WARNING
     **************************************************************************/

    /**
     * Sets the following WordPress options:
     *     rightpress_up_pc_{plugin_key}                        Purchase code
     *     rightpress_up_nag_{plugin_key}                       Display purchase code nag, value is set to new version
     *     rightpress_up_dis_{plugin_key}                       Purchase code nag dismissed for all versions
     *     rightpress_up_nag_{version_key}_{plugin_key}         Purchase code nag dismissed for specific version
     *     rightpress_{context}_nag_{plugin_key}                Display custom nag, value is set to new version
     *     rightpress_{context}_nag_{version_key}_{plugin_key}  Custom nag dismissed for version
     *     rightpress_{context}_nag_t_{plugin_key}              Custom nag title
     *     rightpress_{context}_nag_c_{plugin_key}              Custom nag html content
     *
     * Sets the following WordPress transients:
     *     rightpress_up_note_{plugin_key}_{version_key}        Optional update note
     */

    // Version number
    private static $rightpress_updates_version = '2.2';

    // Object properties
    private $endpoint_url;
    private $plugin_path;
    private $plugin_basename;
    private $plugin_slug;
    private $plugin_key;
    private $purchase_code;
    private $nag_error_message;
    private $nag_value;

    // Define nag contexts
    private static $nag_contexts = array('up', 'exp', 'use', 'c_1', 'c_2', 'c_3', 'c_4', 'c_5');

    /**
     * Initialize update class for plugin
     *
     * @access public
     * @param string $plugin_path
     * @param string $plugin_version
     * @return void
     */
    public static function init($plugin_path, $plugin_version)
    {

        new self($plugin_path, $plugin_version);
    }

    /**
     * Constructor
     *
     * @access public
     * @param string $plugin_path
     * @param string $plugin_version
     * @return void
     */
    public function __construct($plugin_path, $plugin_version)
    {

        $this->endpoint_url     = 'http://updates.rightpress.net/';
        $this->plugin_path      = $plugin_path;
        $this->plugin_version   = $plugin_version;
        $this->plugin_basename  = plugin_basename($this->plugin_path);
        $this->plugin_slug      = $this->get_plugin_slug();
        $this->plugin_key       = $this->get_plugin_key();
        $this->purchase_code    = $this->get_purchase_code();

        // Register plugin with WordPress updater
        add_filter('pre_set_site_transient_update_plugins', array($this, 'register_plugin'), 3);

        // Override WordPress.org Plugin Install API
        add_filter('plugins_api', array($this, 'plugins_api_actions'), 10, 3);

        // Maybe display nags
        if (is_multisite()) {
            add_action('network_admin_notices', array($this, 'maybe_display_nag'));
        }
        else {
            add_action('admin_notices', array($this, 'maybe_display_nag'));
        }

        // Maybe display update note
        add_action('in_plugin_update_message-' . $this->plugin_basename, array($this, 'maybe_display_update_note'));

        // Maybe display activation information inline
        add_action('in_plugin_update_message-' . $this->plugin_basename, array($this, 'maybe_display_activation_information_inline'));

        // Allow changing purchase code
        add_action('admin_enqueue_scripts', array($this, 'enqueue_jquery'));
        add_action('wp_ajax_rightpress_up_pc_change', array($this, 'ajax_rightpress_up_pc_change'));

        if (is_multisite()) {
            add_filter('network_admin_plugin_action_links_' . $this->plugin_basename, array($this, 'display_purchase_code_edit_link'));
        }
        else {
            add_filter('plugin_action_links_' . $this->plugin_basename, array($this, 'display_purchase_code_edit_link'));
        }

        // Some code needs to be executed later
        add_action('init', array($this, 'on_wp_init'), 1);
    }

    /**
     * Executed on WordPress init action
     *
     * @access public
     * @return void
     */
    public function on_wp_init()
    {

        // Intercept Purchase Code submit
        if (isset($_POST['rightpress_updates_purchase_code'])) {
            if (!empty($_POST['rightpress_updates_plugin_slug']) && $_POST['rightpress_updates_plugin_slug'] === $this->plugin_slug) {
                $this->save_purchase_code($_POST['rightpress_updates_purchase_code']);
            }
        }

        // Intercept nag dismiss request
        if (!empty($_REQUEST['rightpress_nag_dismiss'])) {
            if (!empty($_REQUEST['rightpress_plugin_slug']) && $_REQUEST['rightpress_plugin_slug'] === $this->plugin_slug) {
                $this->dismiss_nag();
            }
        }

        // Intercept Purchase Code nag disable request
        if (!empty($_REQUEST['rightpress_disable_up_nags'])) {
            if (!empty($_REQUEST['rightpress_plugin_slug']) && $_REQUEST['rightpress_plugin_slug'] === $this->plugin_slug) {
                $this->disable_purchase_code_nags();
            }
        }
    }

    /**
     * Register plugin with WordPress updater
     *
     * @access public
     * @param object $transient
     * @return array
     */
    public function register_plugin($transient)
    {

        // Get current version
        if (!empty($transient->checked) && is_array($transient->checked) && isset($transient->checked[$this->plugin_basename])) {
            $current_version = $transient->checked[$this->plugin_basename];
        }
        else if (!empty($this->plugin_version)) {
            $current_version = $this->plugin_version;
        }
        else {
            return $transient;
        }

        // Get data from RightPress Update service
        $response = $this->check_for_update($current_version);

        // Get version for nags
        if (is_object($response) && !empty($response->data) && is_object($response->data) && !empty($response->data->new_version)) {
            $nag_version = $response->data->new_version;
        }
        else {
            $nag_version = $current_version;
        }

        // Maybe delete Purchase Code from options
        if (is_object($response) && !empty($response->reset_purchase_code) && $response->reset_purchase_code) {
            $this->reset_purchase_code();
        }

        // Set or unset item support status
        $expired_option_key = 'rightpress_updates_expired_' . $this->plugin_key;

        if (isset($response->is_supported) && !$response->is_supported) {
            update_site_option($expired_option_key, 1);
        }
        else {
            delete_site_option($expired_option_key);
        }

        // Unset nags
        if (is_object($response) && !empty($response->nag_unset) && is_array($response->nag_unset)) {
            foreach ($response->nag_unset as $nag_context_to_unset) {
                $this->remove_nag($nag_context_to_unset);
            }
        }

        // Set nags
        if (is_object($response) && !empty($response->nag_set) && is_array($response->nag_set)) {
            foreach ($response->nag_set as $nag_to_set) {
                if (!empty($nag_to_set->context) && !empty($nag_to_set->title) && !empty($nag_to_set->content) && in_array($nag_to_set->context, self::$nag_contexts, true)) {
                    $this->maybe_add_custom_nag($nag_to_set->context, $nag_to_set->title, $nag_to_set->content, $nag_version);
                }
            }
        }

        // Extend data
        if (is_object($response) && !empty($response->data) && is_object($response->data)) {
            $response->data->plugin = $this->plugin_basename;
        }

        // Add Purchase Code nag if purchase code is not set
        if (empty($this->purchase_code)) {
            $this->maybe_add_purchase_code_nag($nag_version);
        }

        // Register with WordPress updater
        if (is_object($response) && !empty($response->data) && is_object($response->data)) {
            $transient->response[$this->plugin_basename] = $response->data;
        }
        // Suppress wordpress.org plugins with the same name
        else if (isset($transient->response[$this->plugin_basename])) {
            if (strpos($transient->response[$this->plugin_basename]->package, 'wordpress.org') !== false) {
                unset($transient->response[$this->plugin_basename]);
            }
        }

        return $transient;
    }

    /**
     * Override WordPress.org Plugin Install API
     *
     * @acceess public
     * @param mixed $result
     * @param string $action
     * @param object $args
     * @return mixed
     */
    public function plugins_api_actions($result, $action, $args)
    {

        // Check if it's a call for this plugin
        if (empty($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        // Get current plugin version
        $current_version = $this->get_current_plugin_version();

        // Check if plugin version was determined
        if (!$current_version) {
            return $result;
        }

        // Send request to remote service
        $response = $this->remote_post($action, array(
            'current_version'   => $current_version,
            'new_version'       => (!empty($args->new_version) ? $args->new_version : null),
            'purchase_code'     => $this->purchase_code,
        ));

        // Error occurred
        if (!$response || empty($response->data)) {
            return new WP_Error('plugins_api_failed', esc_html__('An unexpected error occurred.', 'rightpress-updates'));
        }

        // Unserialize data object
        $data = unserialize($response->data);

        // Check if data object looks valid
        if ($action === 'plugin_information' && (!is_object($data) || empty($data->name))) {
            return new WP_Error('plugins_api_failed', esc_html__('An unexpected error occurred.', 'rightpress-updates'));
        }

        return $data;
    }

    /**
     * Check for update with RightPress Update service
     *
     * @access public
     * @param string $current_version
     * @return mixed
     */
    public function check_for_update($current_version)
    {

        // Format transient key
        $transient_key = 'rightpress_updates_' . $this->plugin_key;

        // Get cached data
        $response = get_site_transient($transient_key);

        // Invalidate cache due to recent client update
        if (is_object($response) && isset($response->data->new_version) && version_compare($current_version, $response->data->new_version, '>=')) {
            $response = false;
        }

        // Update data from server
        if ($response === false) {

            // Send request
            $response = $this->remote_post('updates', array(
                'current_version'   => $current_version,
                'purchase_code'     => $this->purchase_code,
            ));

            // Cache response if successful
            if (is_object($response)) {

                // Clone object
                $response_to_cache = clone $response;

                // Unset some internal params so we don't repeat same actions multiple times
                $response_to_cache->reset_purchase_code = null;
                $response_to_cache->nag_unset = null;
                $response_to_cache->nag_set = null;

                // Set transient for 15 minutes
                set_site_transient($transient_key, $response_to_cache, 900);
            }
        }

        // Return data
        return $response;
    }

    /**
     * Prepare and send remote post request
     *
     * @access public
     * @param string $action
     * @param array $args
     * @return mixed
     */
    public function remote_post($action, $args = array())
    {

        // Get WordPress version
        global $wp_version;

        // Attempt to get WooCommerce version
        $wc_version = (defined('WC_VERSION') && WC_VERSION) ? WC_VERSION : ((defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) ? WOOCOMMERCE_VERSION : null);

        // Format request url
        $request_url = $this->endpoint_url . $action;

        // Push additional properties to request arguments
        $args['plugin_slug']    = $this->plugin_slug;
        $args['home_url']       = home_url();
        $args['php_version']    = PHP_VERSION;
        $args['wp_version']     = $wp_version;
        $args['wc_version']     = $wc_version;
        $args['rp_version']     = self::$rightpress_updates_version;
        $args['envato_id']      = self::$envato_id;

        // Send request
        $response = wp_remote_post($request_url, array(
            'body'          => $args,
            'user-agent'    => ('WordPress/' . $wp_version),
        ));

        // Check response
        if (is_wp_error($response) || !is_array($response) || empty($response['body'])) {
            return false;
        }

        // Return response body
        return json_decode($response['body']);
    }

    /**
     * Get current plugin version
     *
     * @access public
     * @return mixed
     */
    public function get_current_plugin_version()
    {

        // Load plugin update data
        $update_plugins = get_site_transient('update_plugins');

        // Check if version is set and return it
        if (is_object($update_plugins) && isset($update_plugins->checked) && is_array($update_plugins->checked) && !empty($update_plugins->checked[$this->plugin_basename])) {
            return $update_plugins->checked[$this->plugin_basename];
        }

        return false;
    }

    /**
     * Maybe add purchase code nag
     *
     * @access public
     * @param array $version
     * @return void
     */
    public function maybe_add_purchase_code_nag($version)
    {

        // Admin disabled purchase code nags
        if (get_site_option('rightpress_up_dis_' . $this->plugin_key)) {
            return false;
        }

        // Add nag
        $this->maybe_add_nag('up', $version);
    }

    /**
     * Maybe add custom nag
     *
     * @access public
     * @param string $context
     * @param string $title
     * @param string $content
     * @param array $version
     * @return void
     */
    public function maybe_add_custom_nag($context, $title, $content, $version)
    {

        // Add nag
        if ($this->maybe_add_nag($context, $version)) {

            // Add custom nag title
            update_site_option(('rightpress_' . $context . '_nag_t_' . $this->plugin_key), $title);

            // Add custom nag content
            update_site_option(('rightpress_' . $context . '_nag_c_' . $this->plugin_key), $content);
        }
    }

    /**
     * Maybe add nag
     *
     * @access public
     * @param string $context
     * @param array $version
     * @return bool
     */
    public function maybe_add_nag($context, $version)
    {

        // Get nag key prefix
        $prefix = 'rightpress_' . $context . '_nag_';

        // Get option key
        $option_key = $prefix . $this->plugin_key;

        // Nag is already displayed
        // Second part of the condition is legacy, see issue #49
        if (get_site_option($option_key) || (is_multisite() && get_option($option_key))) {
            return false;
        }

        // Admin has dismissed nag for this version
        $dismissed_nag_option_key = $prefix . self::get_version_key($version) . '_'  . $this->plugin_key;

        // Second part of the condition is legacy, see issue #49
        if (get_site_option($dismissed_nag_option_key) || (is_multisite() && get_option($dismissed_nag_option_key))) {
            return false;
        }

        // Add nag
        update_site_option($option_key, $version);

        // Nag added
        return true;
    }

    /**
     * Remove nag
     *
     * @access public
     * @param string $context
     * @return void
     */
    public function remove_nag($context)
    {

        delete_site_option('rightpress_' . $context . '_nag_' . $this->plugin_key);
        delete_site_option('rightpress_' . $context . '_nag_t_' . $this->plugin_key);
        delete_site_option('rightpress_' . $context . '_nag_c_' . $this->plugin_key);

        // Legacy, see issue #49
        if (is_multisite()) {
            delete_option('rightpress_' . $context . '_nag_' . $this->plugin_key);
            delete_option('rightpress_' . $context . '_nag_t_' . $this->plugin_key);
            delete_option('rightpress_' . $context . '_nag_c_' . $this->plugin_key);
        }
    }

    /**
     * Maybe display nag
     *
     * @access public
     * @return void
     */
    public function maybe_display_nag()
    {

        // Get current plugin version
        $current_version = $this->get_current_plugin_version();

        // Check if plugin version was determined
        if (!$current_version) {
            return;
        }

        // Iterate over nag contexts
        foreach (self::$nag_contexts as $context) {

            // Check if there is a nag to be displayed
            if ($nag_version = get_site_option('rightpress_' . $context . '_nag_' . $this->plugin_key)) {

                // Check if new version nag needs to be displayed
                $is_new_version = version_compare($nag_version, $current_version, '>');

                // Do not print purchase code nag if plugin is bundled with a theme
                // Note: this must be enabled by theme developers using the filter below
                if ($context === 'up' && apply_filters(('rightpress_updates_suppress_nags_' . self::$envato_id), false)) {
                    continue;
                }

                // Print nag
                $this->print_nag($context, $nag_version, $is_new_version);
            }
        }
    }

    /**
     * Print nag
     *
     * @access public
     * @param string $context
     * @param string $nag_version
     * @param bool $is_new_version
     * @return string
     */
    public function print_nag($context, $nag_version, $is_new_version)
    {

        // Purchase code nag
        if ($context === 'up') {

            // Get title
            $title = esc_html__('Automatic Update Setup', 'rightpress-updates');

            // Main text
            if ($is_new_version) {
                $text = esc_html__('There is a new version of %s available. To enable automatic updates, enter your CodeCanyon Purchase Code below.', 'rightpress-updates');
            }
            else {
                $text = esc_html__('%s supports automatic updates. To receive them, enter your CodeCanyon Purchase Code below.', 'rightpress-updates');
            }

            $content = '<div style="margin-bottom: 0.6em; font-size: 13px;">' . sprintf($text, ('<strong>' . $this->get_plugin_name() . '</strong>')) . '</div>';

            // Purchase code validation error
            if (isset($this->nag_error_message) && !empty($this->nag_error_message)) {
                $content .= '<div style="margin-bottom: 0.6em; font-size: 13px; color: red;">' . $this->nag_error_message . '</div>';
            }

            // Open form
            $content .= '<form method="post" style="margin-bottom: 0.6em;">';

            // Field
            $content .= '<input type="text" name="rightpress_updates_purchase_code" value="' . (isset($this->nag_value) ? $this->nag_value : '') . '" placeholder="' . esc_html__('Purchase Code', 'rightpress-updates') . '" style="width: 50%; margin-right: 10px;">';

            // Hidden plugin slug field
            $content .= '<input type="hidden" name="rightpress_updates_plugin_slug" value="' . $this->plugin_slug . '">';

            // Button
            $content .= '<button type="submit" class="button button-primary" title="' . esc_html__('Submit', 'rightpress-updates') . '">' . esc_html__('Submit', 'rightpress-updates') . '</button>';

            // Close form
            $content .= '</form>';

            // Format
            $notes = '<a href="http://url.rightpress.net/purchase-code-help">' . esc_html__('Where do I find my Purchase Code?', 'rightpress-updates') . '</a>';
            $notes .= '&nbsp;&nbsp;&nbsp;';
            $notes .= '<a href="' . add_query_arg(array('rightpress_disable_up_nags' => 'up', 'rightpress_plugin_slug' => $this->plugin_slug)) . '">' . esc_html__('Do not remind me again', 'rightpress-updates') . '</a>';
            $notes .= '&nbsp;&nbsp;&nbsp;';
            $notes .= '<a href="' . add_query_arg(array('rightpress_nag_dismiss' => 'up', 'rightpress_plugin_slug' => $this->plugin_slug, 'rightpress_nag_version' => $nag_version)) . '">' . esc_html__('Hide This Notice', 'rightpress-updates') . '</a>';

            // Wrap and append notes
            $content .= '<div><small>' . $notes . '</small></div>';
        }
        // Custom nag
        else {

            // Custom nags must have title and content set in the database
            $title      = get_site_option('rightpress_' . $context . '_nag_t_' . $this->plugin_key);
            $content    = get_site_option('rightpress_' . $context . '_nag_c_' . $this->plugin_key);

            // Check if title and content are set
            if (empty($title) || empty($content)) {
                return;
            }

            // Content may ask for plugin name and nag dismiss url
            $content = sprintf($content, $this->get_plugin_name(), add_query_arg(array('rightpress_nag_dismiss' => $context, 'rightpress_plugin_slug' => $this->plugin_slug, 'rightpress_nag_version' => $nag_version)));
        }

        // Print styles
        echo '<style>.rightpress-clear-both { clear: both; } .rightpress-updates-update-nag { display: block; text-align: left; background-color: #fff; border-left: 4px solid #ffba00; box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%); } .rightpress-updates-update-nag h3 { margin-top: 0.3em; margin-bottom: 0.6em; }</style>';

        // Print nag
        echo '<div class="rightpress-clear-both"></div><div class="update-nag rightpress-updates-update-nag"><h3>' . $title . '</h3>' . $content . '<div class="rightpress-clear-both"></div></div>';
    }

    /**
     * Dismiss nag
     *
     * @access public
     * @return void
     */
    public function dismiss_nag()
    {

        // Check if nag context is set
        if (!empty($_REQUEST['rightpress_nag_dismiss']) && in_array($_REQUEST['rightpress_nag_dismiss'], self::$nag_contexts, true)) {

            // Get nag context
            $context = $_REQUEST['rightpress_nag_dismiss'];

            // Remove nag
            $this->remove_nag($context);

            // Never show nag for this version again
            if (!empty($_REQUEST['rightpress_nag_version'])) {
                update_site_option(('rightpress_' . $context . '_nag_' . self::get_version_key($_REQUEST['rightpress_nag_version']) . '_'  . $this->plugin_key), 1);
            }
        }

        // Get original page url
        $redirect_url = remove_query_arg(array('rightpress_nag_dismiss', 'rightpress_plugin_slug', 'rightpress_nag_version'));

        // Redirect user and exit
        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Disable Purchase Code nags ("Do not remind me again")
     *
     * @access public
     * @return void
     */
    public function disable_purchase_code_nags()
    {

        // Remove current Purchase Code nag
        $this->remove_nag('up');

        // Never show Purchase Code nags again
        update_site_option(('rightpress_up_dis_' . $this->plugin_key), 1);

        // Get original page url
        $redirect_url = remove_query_arg(array('rightpress_disable_up_nags', 'rightpress_plugin_slug'));

        // Redirect user and exit
        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Process submitted Purchase Code
     *
     * @access public
     * @param string $purchase_code
     * @return void
     */
    public function save_purchase_code($purchase_code)
    {

        // Remove white space
        $purchase_code = trim($purchase_code);

        // Process Purchase Code
        try {

            // No Purchase Code or invalid format
            if (empty($purchase_code) || !$this->purchase_code_has_valid_format($purchase_code)) {
                throw new Exception(esc_html__('Purchase Code format is invalid.', 'rightpress-updates'));
            }

            // Validate Purchase Code
            $result = $this->validate_purchase_code($purchase_code);

            // Unable to verify Purchase Code right now (e.g. server is down)
            if ($result === false || $result === 'error') {
                throw new Exception(esc_html__('Unable to verify Purchase Code right now. Please try again later.', 'rightpress-updates'));
            }
            // Purchase Code is not valid
            else if ($result === 'not_valid') {
                throw new Exception(esc_html__('Purchase Code is not valid.', 'rightpress-updates'));
            }
            // Purchase Code belongs to a different product
            else if ($result === 'bad_product') {
                throw new Exception(esc_html__('Purchase Code belongs to another product.', 'rightpress-updates'));
            }
            // Purchase Code is valid - save it
            else if ($result === 'valid') {

                // Save Purchase Code
                $this->update_purchase_code($purchase_code);

                // Redirect user so that RightPress Updates is loaded with new config
                wp_redirect(add_query_arg(array('rightpress_purchase_code_saved' => '1')));
                exit;
            }
        }
        catch (Exception $e) {

            // Set nag error message and value
            $this->nag_error_message    = $e->getMessage();
            $this->nag_value            = $purchase_code;
        }
    }

    /**
     * Validate Purchase Code
     *
     * @access public
     * @param string $purchase_code
     * @return mixed
     */
    public function validate_purchase_code($purchase_code)
    {

        // Send request to Purchase Code validation service
        $response = $this->remote_post('purchase_code_validation', array(
            'purchase_code' => $purchase_code,
        ));

        // Check if request succeeded
        if ($response && is_object($response) && !empty($response->result)) {
            if (in_array($response->result, array('valid', 'not_valid', 'bad_product'))) {
                return $response->result;
            }
        }

        return false;
    }

    /**
     * Check if purchase code is of valid format
     *
     * @access public
     * @param string $purchase_code
     * @return bool
     */
    public function purchase_code_has_valid_format($purchase_code)
    {

        return (bool) preg_match('/[0-9a-zA-Z]{8}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{12}/', $purchase_code);
    }

    /**
     * Get purchase code
     *
     * @access public
     * @return mixed
     */
    public function get_purchase_code()
    {

        // Format option key
        $option_key = 'rightpress_up_pc_' . $this->plugin_key;

        // Get purchase code from database
        $purchase_code = get_site_option($option_key, '');

        // Legacy, see issue #49
        if (!$purchase_code && is_multisite()) {
            $purchase_code = get_option($option_key, '');
        }

        // Return purchase code
        return $purchase_code;
    }

    /**
     * Update purchase code
     *
     * @access public
     * @param string $purchase_code
     * @return void
     */
    public function update_purchase_code($purchase_code)
    {

        $this->purchase_code = $purchase_code;

        $option_key = 'rightpress_up_pc_' . $this->plugin_key;

        // Update purchase code
        if (!empty($purchase_code)) {
            update_site_option($option_key, $purchase_code);
        }
        // Delete purchase code
        else {

            delete_site_option($option_key);

            // Legacy, see issue #49
            if (is_multisite()) {
                delete_option($option_key);
            }
        }

        // Clear nags
        foreach (self::$nag_contexts as $nag_context) {
            $this->remove_nag($nag_context);
        }

        // Clear updates cache
        delete_site_transient('rightpress_updates_' . $this->plugin_key);

        // Force WordPress to check for updates again
        set_site_transient('update_plugins', null);
    }

    /**
     * Reset purchase code
     *
     * @access public
     * @return void
     */
    public function reset_purchase_code()
    {

        $this->update_purchase_code('');
    }

    /**
     * Get plugin slug
     *
     * @access public
     * @return string
     */
    public function get_plugin_slug()
    {

        return dirname($this->plugin_basename);
    }

    /**
     * Get plugin key for use in WordPress option key
     *
     * @access public
     * @return string
     */
    public function get_plugin_key()
    {

        return preg_replace('/[^A-Za-z_]/', '', str_replace('-', '_', substr($this->plugin_slug, 0, 32)));
    }

    /**
     * Get version key for use in WordPress option key
     *
     * @access public
     * @param string $version
     * @return string
     */
    public static function get_version_key($version)
    {

        return str_replace('.', '_', $version);
    }

    /**
     * Get plugin name
     *
     * @access public
     * @return string
     */
    public function get_plugin_name()
    {

        $plugin_data = get_plugin_data($this->plugin_path);
        return ((is_array($plugin_data) && !empty($plugin_data['Name'])) ? $plugin_data['Name'] : 'Plugin');
    }

    /**
     * Ensure jQuery is enqueued
     *
     * @access public
     * @return void
     */
    public function enqueue_jquery()
    {

        wp_enqueue_script('jquery');
    }

    /**
     * Display purchase code edit link
     *
     * @access public
     * @param array $links
     * @return array
     */
    public function display_purchase_code_edit_link($links)
    {

        // Format link
        $link = '<a class="rightpress_up_pc_' . $this->plugin_key . '" style="cursor: pointer; ' . (empty($this->purchase_code) ? 'font-weight: 700;' : '') . '">' . esc_html__('Purchase Code', 'rightpress-updates') . '</a>';

        // Open script
        $link .= '<script type="text/javascript" style="display: none;">';

        // Javascript handler
        $link .= "
            jQuery(document).ready(function() {

                'use strict';

                // Define current purchase code
                var current_purchase_code = '$this->purchase_code';

                // Bind click action
                jQuery('.rightpress_up_pc_$this->plugin_key').on('click', function() {
                    var prompt_text = '" . esc_html__('Enter your Envato Purchase Code to enable automatic updates.', 'rightpress-updates') . "';
                    handle_purchase_code_change(prompt_text, current_purchase_code);
                });

                // Handle purchase code change
                function handle_purchase_code_change(prompt_text, purchase_code)
                {
                    // Display dialog
                    var new_purchase_code = prompt(prompt_text, purchase_code);

                    // Prompt cancelled or purchase code was not changed
                    if (new_purchase_code === null || new_purchase_code === current_purchase_code) {
                        return;
                    }

                    // Send request to server
                    jQuery.post(
                        '" . admin_url('admin-ajax.php') . "',
                        {
                            'action':           'rightpress_up_pc_change',
                            'plugin_key':       '$this->plugin_key',
                            'purchase_code':    new_purchase_code
                        },
                        function(response) {

                            // Purchase code updated
                            if (response.indexOf('rightpress_up_pc_change_valid') > -1) {
                                alert('" . esc_html__('Purchase Code has been successfully updated.', 'rightpress-updates') . "');
                            }
                            // Purchase code reset
                            else if (response.indexOf('rightpress_up_pc_change_reset') > -1) {
                                alert('" . esc_html__('Purchase Code has been successfully reset.', 'rightpress-updates') . "');
                            }
                            // Purchase code is not valid
                            else if (response.indexOf('rightpress_up_pc_change_not_valid') > -1) {
                                var prompt_text = '" . esc_html__('Purchase Code is not valid.', 'rightpress-updates') . " " . esc_html__('Please fix it and try again.', 'rightpress-updates') . "';
                                handle_purchase_code_change(prompt_text, new_purchase_code);
                                return;
                            }
                            // Purchase code is not for this product
                            else if (response.indexOf('rightpress_up_pc_change_bad_product') > -1) {
                                var prompt_text = '" . esc_html__('Purchase Code belongs to another product.', 'rightpress-updates') . " " . esc_html__('Please change it and try again.', 'rightpress-updates') . "';
                                handle_purchase_code_change(prompt_text, new_purchase_code);
                                return;
                            }
                            // Error occurred
                            else {
                                alert('" . esc_html__('Unable to verify Purchase Code right now. Please try again later.', 'rightpress-updates') . "');
                            }

                            // Reload page to start fresh
                            window.location.reload();
                        }
                    );
                }
            });
        ";

        // Close script
        $link .= '</script>';

        // Add to links array
        array_unshift($links, $link);

        // Return links
        return $links;
    }

    /**
     * Handle purchase code change Ajax request
     *
     * @access public
     * @return void
     */
    public function ajax_rightpress_up_pc_change()
    {

        // Check if plugin key and new purchase code is set
        if (empty($_POST['plugin_key']) || !isset($_POST['purchase_code'])) {
            echo 'rightpress_up_pc_change_error';
            exit;
        }

        // Check if plugin key is for current plugin or let other instances handle this request
        if ($_POST['plugin_key'] !== $this->plugin_key) {
            return;
        }

        // Purchase code was cleared
        if (empty($_POST['purchase_code'])) {
            $this->reset_purchase_code();
            echo 'rightpress_up_pc_change_reset';
            exit;
        }

        // Check if purchase code has correct format
        if (!$this->purchase_code_has_valid_format($_POST['purchase_code'])) {
            echo 'rightpress_up_pc_change_not_valid';
            exit;
        }

        // Validate new purchase code
        $result = $this->validate_purchase_code($_POST['purchase_code']);

        // Error occurred
        if ($result === false || $result === 'error') {
            echo 'rightpress_up_pc_change_error';
            exit;
        }

        // Purchase code is not valid or belongs to a different product
        if ($result === 'not_valid' || $result === 'bad_product') {
            echo 'rightpress_up_pc_change_' . $result;
            exit;
        }

        // Purchase code is valid
        if ($result === 'valid') {
            $this->update_purchase_code($_POST['purchase_code']);
            echo 'rightpress_up_pc_change_valid';
            exit;
        }

        // Something went wrong if we reached this point
        echo 'rightpress_up_pc_change_error';
        exit;
    }

    /**
     * Maybe display update note
     *
     * @access public
     * @param object $args
     * @return void
     */
    public function maybe_display_update_note($args)
    {

        if (is_array($args)) {
            $args = (object) $args;
        }

        // New version must be set
        if (!is_object($args) || empty($args->new_version)) {
            return;
        }

        // Get transient key
        $transient_key = 'rightpress_up_note_' . $this->plugin_key . '_' . self::get_version_key($args->new_version);

        // Get update note from database
        $update_note = get_site_transient($transient_key);

        // Check if update note is already in database
        if ($update_note === false) {

            // Get update note
            $update_note = $this->get_update_note($args);

            // Check if update note was retrieved
            if ($update_note !== false) {

                // Store update note in database
                set_site_transient($transient_key, $update_note, DAY_IN_SECONDS);
            }
            // Failed getting update note
            else {

                // Display empty string for now, will retry later
                $update_note = '';
            }
        }

        // Display update note
        echo $update_note;
    }

    /**
     * Get update note
     *
     * @access public
     * @param object $args
     * @return string
     */
    public function get_update_note($args)
    {

        // Get plugin information
        $plugin_information = $this->plugins_api_actions(false, 'plugin_information', $args);

        // Request failed
        if (is_wp_error($plugin_information) || !is_object($plugin_information) || !isset($plugin_information->rightpress_update_note)) {
            return false;
        }

        // Get update note
        $update_note = trim($plugin_information->rightpress_update_note);

        // Check if update note is not empty
        if (empty($update_note)) {
            return '';
        }

        // Build html
        // Styling adapted from WooCommerce for constant user experience
        $html = "
            </p>
            <style style=\"display: none;\">
                .rightpress_update_note_{$this->plugin_key} {
                    display: block;
                    margin: 9px 0;
                    padding: 1em;
                    background: #d54d21;
                    color: #fff;
                    font-weight: normal;
                }
                .rightpress_update_note_{$this->plugin_key} a {
                    color: #fff;
                    text-decoration: underline;
                }
                .rightpress_update_note_{$this->plugin_key}:before {
                    content: \"\\f348\";
                    display: inline-block;
                    font: 400 18px/1 dashicons;
                    speak: none;
                    margin: 0 8px 0 -2px;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                    vertical-align: top;
                }
                .rightpress_update_note_dummy {
                    display: none!important;
                }
            </style>
            <div class=\"rightpress_update_note_{$this->plugin_key}\">
                " . wp_kses_post(preg_replace('~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $update_note)) . "
            </div>
            <p class=\"rightpress_update_note_dummy\">
        ";

        // Return update note
        return $html;
    }

    /**
     * Maybe display activation information inline
     *
     * @access public
     * @param object $args
     * @return void
     */
    public function maybe_display_activation_information_inline($args)
    {

        if (is_array($args)) {
            $args = (object) $args;
        }

        // New version must be set but package must be unavailable
        if (!is_object($args) || empty($args->new_version) || isset($args->package)) {
            return;
        }

        // Purchase Code is not set
        if (empty($this->purchase_code)) {
            echo ' <em>' . sprintf(esc_html__('You must enter your Purchase Code %s to enable automatic updates.', 'rightpress-updates'), ('<a class="rightpress_up_pc_' . $this->plugin_key . '" href="#">' . esc_html__('here', 'rightpress-updates') . '</a>')) . ' <a href="http://url.rightpress.net/purchase-code-help">' . esc_html__('Where do I find my Purchase Code?', 'rightpress-updates') . '</a></em>';
        }
        // Support expired
        else if (get_site_option('rightpress_updates_expired_' . $this->plugin_key)) {
            echo ' <em>' . sprintf(esc_html__('RightPress no longer serves automatic updates as your %s has expired.', 'rightpress-updates'), ('<a href="http://url.rightpress.net/automatic-updates-help">' . __('support subscription', 'rightpress-updates') . '</a>')) . ' ' . sprintf(esc_html__('You can %1$s your support subscription or use %2$s to keep this plugin updated.', 'rightpress-updates'), ('<a href="http://url.rightpress.net/' . self::$envato_id . '-product">' . esc_html__('renew', 'rightpress-updates') . '</a>'), ('<a href="http://url.rightpress.net/updates-help">' . esc_html__('other methods', 'rightpress-updates') . '</a>')) . '</em>';
        }
    }






}
}
