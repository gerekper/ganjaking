<?php
/*
  Plugin Name: WP Reset PRO
  Plugin URI: https://wpreset.com/
  Description: Easily undo any change on the site by restoring a snapshot, or reset the entire site or any of its parts to the default values.
  Version: 5.78
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  Text Domain: wp-reset

  Copyright 2015 - 2020  Web factory Ltd  (email: wpreset@webfactoryltd.com)

  This program is NOT free software.

  ██╗    ██╗███████╗██████╗ ███████╗ █████╗  ██████╗████████╗ ██████╗ ██████╗ ██╗   ██╗
  ██║    ██║██╔════╝██╔══██╗██╔════╝██╔══██╗██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗╚██╗ ██╔╝
  ██║ █╗ ██║█████╗  ██████╔╝█████╗  ███████║██║        ██║   ██║   ██║██████╔╝ ╚████╔╝
  ██║███╗██║██╔══╝  ██╔══██╗██╔══╝  ██╔══██║██║        ██║   ██║   ██║██╔══██╗  ╚██╔╝
  ╚███╔███╔╝███████╗██████╔╝██║     ██║  ██║╚██████╗   ██║   ╚██████╔╝██║  ██║   ██║
   ╚══╝╚══╝ ╚══════╝╚═════╝ ╚═╝     ╚═╝  ╚═╝ ╚═════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝   ╚═╝
*/

// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}

define('WP_RESET_FILE', __FILE__);

require_once dirname(__FILE__) . '/wp-reset-utility.php';
require_once dirname(__FILE__) . '/wp-reset-tools.php';
require_once dirname(__FILE__) . '/wp-reset-collections.php';
require_once dirname(__FILE__) . '/wp-reset-cloud.php';
require_once dirname(__FILE__) . '/wf-licensing.php';

// load WP-CLI commands, if needed
if (defined('WP_CLI') && WP_CLI) {
    require_once dirname(__FILE__) . '/wp-reset-cli.php';
}

class WP_Reset
{
    protected static $instance = null;
    public $version = 0;
    public $er_version = 1.1;
    public $plugin_url = '';
    public $plugin_dir = '';
    public $licensing_servers = array('https://dashboard.wpreset.com/api/');
    public $snapshots_folder = 'wp-reset-snapshots-export';
    public $autosnapshots_folder = 'wp-reset-autosnapshots';
    protected $options = array();
    public $log = false;
    private $delete_count = 0;
    public $core_tables = array('commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'term_relationships', 'term_taxonomy', 'termmeta', 'terms', 'usermeta', 'users');
    public $cloud_services = array('dropbox' => 'Dropbox', 'gdrive' => 'Google Drive', 'pcloud' => 'pCloud', 'pcloudeu' => 'pCloud EU', 'icedrive' => 'Icedrive');

    /**
     * Creates a new WP_Reset object and implements singleton
     *
     * @return WP_Reset
     */
    static function getInstance()
    {
        if (!is_a(self::$instance, 'WP_Reset')) {
            self::$instance = new WP_Reset();
        }

        return self::$instance;
    } // getInstance


    /**
     * Initialize properties, hook to filters and actions
     *
     * @return null
     */
    private function __construct()
    {
        $this->version = $this->get_plugin_version();
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->load_options();

        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_ajax_wp_reset_dismiss_notice', array($this, 'ajax_dismiss_notice'));
        add_action('wp_ajax_wp_reset_run_tool', array($this, 'ajax_run_tool'));
        add_action('wp_ajax_wp_reset_submit_survey', array($this, 'ajax_submit_survey'));
        add_action('admin_print_scripts', array($this, 'remove_admin_notices'));
        add_action('admin_footer', array($this, 'admin_footer'));
        add_action('admin_notices', array($this, 'notice_successful_reset'));

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'plugin_meta_links'), 10, 2);
        add_filter('admin_footer_text', array($this, 'admin_footer_text'));
        add_action('admin_action_wpr_clear_log', array($this, 'clear_log'));
        add_action('admin_action_wpr_delete_temporary_files', array($this, 'delete_temporary_files'));
        add_action('admin_action_wpr_delete_snapshot_tables', array($this, 'delete_snapshot_tables'));
        add_action('admin_action_wpr_clear_autouploader', array($this, 'autosnapshots_uploader_reset'));
        add_action('wp_before_admin_bar_render', array($this, 'admin_bar'));

        add_action('plugins_loaded', array($this, 'admin_actions'));

        $this->prune_autosnapshots();

        $this->core_tables = array_map(function ($tbl) {
            global $wpdb;
            return $wpdb->prefix . $tbl;
        }, $this->core_tables);
    } // __construct


    /**
     * Get plugin version from file header
     *
     * @return string
     */
    function get_plugin_version()
    {
        $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');

        return $plugin_data['version'];
    } // get_plugin_version


    /**
     * Actions to run on load, but init would be too early as not all classes are initialized
     *
     * @return null
     */
    function admin_actions()
    {
        global $wp_reset_cloud, $wp_reset_js_notice, $wp_reset_licensing;

        $wp_reset_cloud = new WP_Reset_Cloud();
        $options = $this->get_options();

        $wp_reset_licensing = new WF_Licensing_WPR(array(
            'prefix' => 'wpr',
            'licensing_servers' => $this->licensing_servers,
            'version' => $this->version,
            'plugin_file' => __FILE__,
            'skip_hooks' => false,
            'debug' => false,
            'js_folder' => plugin_dir_url(__FILE__) . '/js/'
        ));

        add_filter('wf_licensing_license_formatted_wpr', function ($return) {
            return str_replace('Wpr', 'WP Reset', $return);
        });

        $this->update_license_storage();

        add_action('wf_licensing_wprvalidate_ajax', function () {
            global $wp_reset_licensing, $wp_reset_collections;
            if ($wp_reset_licensing->is_active()) {
                $wp_reset_collections->get_collections(true);
            }
        });

        if ($wp_reset_licensing->is_active('wpr_cloud')) {
            $this->cloud_services = array_merge(array('wpreset' => 'WP Reset Cloud'), $this->cloud_services);
        } else {
            if ($options['cloud_service'] == 'wpreset') {
                $options['cloud_service'] = 'none';
                $this->update_options('options', $options);
            }
        }

        if (isset($_GET['wpr_wl'])) {
            if ($_GET['wpr_wl'] == 'true') {
                $options['whitelabel'] = true;
            } else {
                $options['whitelabel'] = false;
            }
            $this->update_options('options', $options);
        }

        if (isset($_GET['wpr_debug'])) {
            if ($_GET['wpr_debug'] == 'true') {
                $options['debug'] = true;
            } else {
                $options['debug'] = false;
            }
            $this->update_options('options', $options);
        }

        if (isset($_GET['authorize_cloud'])) {

            $result = $wp_reset_cloud->cloud_authorize_get_token($_GET['authorize_cloud'], $_GET['code']);
            switch ($_GET['authorize_cloud']) {
                case 'dropbox':
                    $cloud_name = 'Dropbox';
                    break;
                case 'gdrive':
                    $cloud_name = 'Google Drive';
                    break;
                case 'pcloud':
                    $cloud_name = 'pCloud';
                    break;
                case 'pcloudeu':
                    $cloud_name = 'pCloud EU';
                    break;
                case 'icedrive':
                    $cloud_name = 'Icedrive';
                    break;
            }

            if (is_wp_error($result)) {
                $wp_reset_js_notice = array('type' => 'error', 'text' => $result->get_error_message());
            } else {
                $wp_reset_js_notice = array('type' => 'success', 'text' => $cloud_name . ' connected successfully!');
            }
        }
    } // admin_actions


    function update_license_storage()
    {
        global $wp_reset_licensing;

        $new = array();

        // nothing to update
        if (!array_key_exists('license', $this->options)) {
            return false;
        }

        $new['license_key'] = $this->options['license']['license_key'];
        if ($this->options['license']['license_active']) {
            $new['error'] = '';
        } else {
            $new['error'] = 'Unknown error. Please reactivate the license.';
        }
        $new['valid_until'] = $this->options['license']['license_expires'];
        $new['last_check'] = time();
        $new['name'] = $this->options['license']['license_type'];
        $new['meta'] = array();

        if ($wp_reset_licensing->update_license($new)) {
            unset($this->options['license']);
            update_option('wp-reset', $this->options);

            return true;
        }

        return false;
    } // update_license_storage


    /**
     * Load and prepare the options array. If needed create a new DB entry.
     *
     * @return array
     */
    private function load_options()
    {
        $options = get_option('wp-reset', array('options' => array()));
        $change = false;

        if (empty($options['meta'])) {
            $options['meta'] = array('first_version' => $this->version, 'first_install' => current_time('timestamp', true), 'reset_count' => 0);
            $change = true;
        }

        if (!isset($options['dismissed_notices']) || !is_array($options['dismissed_notices'])) {
            $options['dismissed_notices'] = array();
            $change = true;
        }

        if (!isset($options['autouploader']) || !is_array($options['autouploader'])) {
            $options['autouploader'] = array();
            $change = true;
        }

        $def_options = array('tools_snapshots' => false, 'events_snapshots' => false, 'snapshots_autoupload' => false, 'autosnapshots_autoupload' => false, 'snapshots_upload_delete' => false, 'scheduled_snapshots' => false, 'prune_snapshots' => false, 'prune_snapshots_details' => 'days-5', 'adminbar_snapshots' => true, 'optimize_tables' => false, 'snapshots_size_alert' => 1000, 'throttle_ajax' => false, 'fix_datetime' => false, 'alternate_db_connection' => false, 'ajax_snapshots_export' => false, 'cloud_snapshots' => false, 'onboarding_done' => false, 'whitelabel' => false, 'debug' => false, 'cloud_service' => 'none', 'cloud_data' => array('dropbox' => false, 'gdrive' => false, 'icedrive' => false));
        
        if(!array_key_exists('icedrive', $options['options']['cloud_data'])){
            $options['options']['cloud_data']['icedrive'] = false;
        }

        if(!array_key_exists('alternate_db_connection', $options['options'])){
            $options['options']['alternate_db_connection'] = false;
        }

        if (sizeof($options['options']) < sizeof($def_options)) {
            $options['options'] = array_merge($def_options, (array) $options['options']);
            $change = true;
        }
        
        if ($change) {
            update_option('wp-reset', $options, true);
        }

        $this->options = $options;
        return $options;
    } // load_options


    /**
     * Log WP Reset event
     *
     * @return array
     */
    function log($type = 'success', $message = false)
    {
        if (!$this->log) {
            $this->log = get_option('wp-reset-log', array());
        }

        if (count($this->log) > 1000) {
            $this->log = array_slice($this->log, -1000, 1000, true);
        }

        if ($message !== false) {
            $this->log[] = array('time' => time(), 'type' => $type, 'message' => $message);
        }
        update_option('wp-reset-log', $this->log);
    }


    /**
     * Get log
     *
     * @return array
     */
    function get_log()
    {
        return get_option('wp-reset-log', array());
    }

    /**
     * Get log
     *
     * @return array
     */
    function get_autouploader()
    {
        $this->load_options();
        return $this->options['autouploader'];
    }


    /**
     * Get formatted snapshot name for log entries
     *
     * @return array
     */
    function log_format_snapshot_name($uid)
    {
        return '"' . $uid . '"';
    }


    /**
     * Clear log
     *
     * @return array
     */
    function clear_log()
    {
        update_option('wp-reset-log', array());

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        }

        return true;
    }

    /**
     * Get meta part of plugin options
     *
     * @return array
     */
    function get_meta()
    {
        return $this->options['meta'];
    } // get_meta


    /**
     * Get license part of plugin options
     *
     * @return array
     */
    function get_license()
    {
        global $wp_reset_licensing;
        return $wp_reset_licensing->get_license();
    } // get_license


    /**
     * Get all dismissed notices, or check for one specific notice
     *
     * @param string  $notice_name  Optional. Check if specified notice is dismissed.
     *
     * @return bool|array
     */
    function get_dismissed_notices($notice_name = '')
    {
        $notices = $this->options['dismissed_notices'];

        if (empty($notice_name)) {
            return $notices;
        } else {
            if (empty($notices[$notice_name])) {
                return false;
            } else {
                return true;
            }
        }
    } // get_dismissed_notices


    /**
     * Get options part of plugin options
     *
     * @return array
     */
    function get_options()
    {
        return $this->options['options'];
    } // get_options


    /**
     * Get all plugin options
     *
     * @return array
     */
    function get_all_options()
    {
        return $this->options;
    } // get_all_options


    /**
     * Update specified plugin options key
     *
     * @param string  $key   Data to save.
     * @param string  $data  Option key.
     *
     * @return bool
     */
    function update_options($key, $data)
    {
        if (false === in_array($key, array('meta', 'license', 'dismissed_notices', 'options', 'autouploader'))) {
            user_error('Unknown options key.', E_USER_ERROR);
            return false;
        }

        $this->options[$key] = $data;
        $tmp = update_option('wp-reset', $this->options);

        return $tmp;
    } // set_options


    /**
     * Add plugin menu entry under Tools menu
     *
     * @return null
     */
    function admin_menu()
    {
        add_management_page(__('WP Reset PRO', 'wp-reset'), __('WP Reset PRO', 'wp-reset'), 'administrator', 'wp-reset', array($this, 'plugin_page'));
    } // admin_menu


    /**
     * Dismiss notice via AJAX call
     *
     * @return null
     */
    function ajax_dismiss_notice()
    {
        check_ajax_referer('wp-reset_dismiss_notice');

        if (!current_user_can('administrator')) {
            wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
        }

        $notice_name = trim(@$_GET['notice_name']);
        if (!$this->dismiss_notice($notice_name)) {
            wp_send_json_error(__('Notice is already dismissed.', 'wp-reset'));
        } else {
            wp_send_json_success();
        }
    } // ajax_dismiss_notice


    /**
     * Dismiss notice by adding it to dismissed_notices options array
     *
     * @param string  $notice_name  Notice to dismiss.
     *
     * @return bool
     */
    function dismiss_notice($notice_name)
    {
        if ($this->get_dismissed_notices($notice_name)) {
            return false;
        } else {
            $notices = $this->get_dismissed_notices();
            $notices[$notice_name] = true;
            $this->update_options('dismissed_notices', $notices);
            return true;
        }
    } // dismiss_notice


    /**
     * Returns all WP pointers
     *
     * @return array
     */
    function get_pointers()
    {
        $pointers = array();

        $pointers['welcome'] = array('target' => '#menu-tools', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800;">WP Reset PRO</b> plugin!<br>Open <a href="' . admin_url('tools.php?page=wp-reset') . '">Tools - WP Reset PRO</a> to access resetting tools and start developing &amp; debugging faster.');

        return $pointers;
    } // get_pointers


    /**
     * Enqueue CSS and JS files
     *
     * @return null
     */
    function admin_enqueue_scripts($hook)
    {
        global $wp_reset_collections, $wp_reset_js_notice, $wp_reset_licensing, $wp_reset_cloud;
        // welcome pointer is shown on all pages except WPR, to admins, until dismissed
        $pointers = $this->get_pointers();
        $dismissed_notices = $this->get_dismissed_notices();
        $license = $wp_reset_licensing->get_license();
        $options = $this->get_options();
        $current_user = wp_get_current_user();

        foreach ($dismissed_notices as $notice_name => $tmp) {
            if ($tmp) {
                unset($pointers[$notice_name]);
            }
        } // foreach

        if (!empty($pointers) && !$this->is_plugin_page() && current_user_can('administrator')) {
            $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('wp-reset_dismiss_notice');

            wp_enqueue_style('wp-pointer');

            wp_enqueue_script('wp-reset-pointers', $this->plugin_url . 'js/wp-reset-pointers.js', array('jquery'), $this->version, true);
            wp_enqueue_script('wp-pointer');
            wp_localize_script('wp-pointer', 'wp_reset_pointers', $pointers);
        }

        if (!$this->is_plugin_page() && !$options['adminbar_snapshots']) {
            return;
        }

        $snapshots = $this->get_snapshots();
        $cloud_snapshots = $wp_reset_cloud->get_cloud_snapshots();
        $pending_autoupload_snaphots = false;
        foreach ($snapshots as $uid => $snapshot) {
            if (array_key_exists('autoupload', $snapshot) && $snapshot['autoupload'] == true && !array_key_exists($uid, $cloud_snapshots)) {
                $pending_autoupload_snaphots = true;
            }
        }

        if (!$pending_autoupload_snaphots || !array_key_exists($options['cloud_service'], $this->cloud_services)) {
            $options['autosnapshots_autoupload'] = false;
        }

        $js_localize = array(
            'undocumented_error' => __('An undocumented error has occurred. Please refresh the page and try again.', 'wp-reset'),
            'documented_error' => __('An error has occurred.', 'wp-reset'),
            'plugin_name' => __('WP Reset PRO', 'wp-reset'),
            'is_plugin_page' => (int) $this->is_plugin_page(),
            'autouploader_key' => 'wpr_autouploader_time_' . preg_replace("/[^a-zA-Z0-9]+/", "", home_url()),
            'whitelabel' => (int) !WP_Reset_Utility::whitelabel_filter(),
            'settings_url' => admin_url('tools.php?page=wp-reset'),
            'icon_url' => $this->plugin_url . 'img/wp-reset-icon.png',
            'invalid_confirmation' => __('Please type "reset" in the confirmation field.', 'wp-reset'),
            'invalid_confirmation_title' => __('Invalid confirmation', 'wp-reset'),
            'cancel_button' => __('Cancel', 'wp-reset'),
            'ok_button' => __('OK', 'wp-reset'),
            'confirm_title' => __('Are you sure you want to proceed?', 'wp-reset'),
            'doing_reset' => __('Resetting in progress. Please wait.', 'wp-reset'),
            'snapshots_autoupload' => $options['snapshots_autoupload'],
            'autosnapshots_autoupload' => $options['autosnapshots_autoupload'],
            'snapshot_success' => __('Snapshot created', 'wp-reset'),
            'snapshot_wait' => __('Creating snapshot. Please wait.', 'wp-reset'),
            'snapshot_confirm' => __('Create snapshot', 'wp-reset'),
            'snapshot_importing' => __('Importing snapshot. Please wait.', 'wp-reset'),
            'snapshot_imported' => __('Snapshot Imported.', 'wp-reset'),
            'snapshot_placeholder' => __('Snapshot name or brief description, ie: before plugin install', 'wp-reset'),
            'snapshot_text' => __('Enter snapshot name or brief description', 'wp-reset'),
            'snapshot_title' => __('Create a new snapshot', 'wp-reset'),
            'export_wait' => __('Exporting snapshot. Please wait.', 'wp-reset'),
            'export_success' => __('Snapshot exported', 'wp-reset'),
            'import_wait' => __('Importing snapshot. Please wait.', 'wp-reset'),
            'import_success' => __('Snapshot imported', 'wp-reset'),
            'snapshots_delete_confirm' => __('Are you sure you want to delete all your user created and automatic snapshots?', 'wp-reset'),
            'collection_add_success' => __('New collection added!', 'wp-reset'),
            'collection_add_wait' => __('Creating collection. Please wait.', 'wp-reset'),
            'collection_add_confirm' => __('Add new collection', 'wp-reset'),
            'collection_add_placeholder' => __('Collection name, ie: plugins for client sites', 'wp-reset'),
            'collection_add_text' => __('Enter collection name or brief description', 'wp-reset'),
            'collection_add_title' => __('Add new collection', 'wp-reset'),
            'cloud_snapshot_uploading' => __('Snapshot uploading to cloud', 'wp-reset'),
            'cloud_snapshot_uploaded' => __('Snapshot uploaded to cloud', 'wp-reset'),
            'cloud_snapshot_downloading' => __('Downloading snapshot from cloud', 'wp-reset'),
            'cloud_snapshot_downloaded' => __('Snapshot downloaded from cloud', 'wp-reset'),
            'cloud_snapshot_deleting' => __('Deleting snapshot from cloud', 'wp-reset'),
            'cloud_snapshot_deleted' => __('Snapshot deleted from cloud', 'wp-reset'),
            'cloud_snapshots_refresh' => __('Refreshing cloud snapshots', 'wp-reset'),
            'cloud_snapshots_refreshed' => __('Snapshots refreshed', 'wp-reset'),
            'max_upload_size' => wp_max_upload_size(),
            'nonce_dismiss_notice' => wp_create_nonce('wp-reset_dismiss_notice'),
            'nonce_run_tool' => wp_create_nonce('wp-reset_run_tool'),
            'nonce_do_reset' => wp_create_nonce('wp-reset_do_reset'),
            'cloud_service' => array_key_exists($options['cloud_service'], $this->cloud_services) ? 1 : 0
        );

        if ($this->is_plugin_page()) {
            wp_enqueue_style('plugin-install');
            wp_enqueue_style('wp-reset', $this->plugin_url . 'css/wp-reset.css', array(), $this->version);
            wp_enqueue_script('plugin-install');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-position');
            add_thickbox();

            $support_text = 'My site details: WP ' . get_bloginfo('version') . ', WPR v' . $this->get_plugin_version() . ', ';
            if (!empty($license['license_key'])) {
                $support_text .= 'license key: ' . $license['license_key'] . '.';
            } else {
                $support_text .= 'no license info.';
            }
            if (strtolower($current_user->display_name) != 'admin' && strtolower($current_user->display_name) != 'administrator') {
                $support_name = $current_user->display_name;
            } else {
                $support_name = '';
            }

            $plugins = get_plugins();
            $active_plugins = get_option('active_plugins');

            $installed_plugins = array();
            foreach ($plugins as $plugin => $plugin_data) {
                if ($plugin == 'wp-reset/wp-reset.php') {
                    continue;
                }
                $plugin_slug = explode('/', $plugin);
                $installed_plugins[] = array(
                    'slug' => $plugin_slug[0],
                    'name' => $plugin_data['Name'],
                    'active' => in_array($plugin, $active_plugins) ? 1 : 0
                );
            }

            $installed_themes = array();
            $themes = wp_get_themes();
            foreach ($themes as $theme => $theme_data) {
                $installed_themes[] = array(
                    'slug' => $theme,
                    'name' => $theme_data['Name'],
                    'child_theme' => $theme != $theme_data['Template'] ? true : false,
                    'template' => $theme_data['Template']
                );
            }

            if ($wp_reset_licensing->is_active()) {
                $collections = $wp_reset_collections->get_collections_keyed();

                $js_localize = array_merge($js_localize, array(
                    'collections' => $collections,
                    'installed_plugins' => $installed_plugins,
                    'installed_themes' => $installed_themes,
                    'activating' => __('Activating', 'wp-reset'),
                    'deactivating' => __('Deactivating', 'wp-reset'),
                    'deleting' => __('Deleting', 'wp-reset'),
                    'installing' => __('Installing', 'wp-reset'),
                    'activating_license' => __('Attempting to activate license for ', 'wp-reset'),
                    'activating_license_failed' => __('Activating License Failed for ', 'wp-reset'),
                    'activating_license_unknown' => __('Licence activation method unknown for ', 'wp-reset'),
                    'activate_failed' => __('Could not activate', 'wp-reset'),
                    'deactivate_failed' => __('Could not deactivate', 'wp-reset'),
                    'delete_failed' => __('Could not delete', 'wp-reset'),
                    'install_failed' => __('Could not install', 'wp-reset'),
                    'install_failed_existing' => __('is already installed', 'wp-reset'),
                    'admin_email' => get_bloginfo('admin_email'),
                    'onboarding_done' => $options['onboarding_done']
                ));

                if (!empty($wp_reset_js_notice)) {
                    $js_localize['js_notice'] = $wp_reset_js_notice;
                }
            }

            $tools_autosnapshot = array(
                'reset_theme_options' => 'Before running the reset theme options tool',
                'delete_transients' => 'Before running the delete transients tool',
                'delete_content' => 'Before running the delete content tool',
                'delete_widgets' => 'Before running the delete widgets tool',
                'truncate_custom_tables' => 'Before running the empty tables tool',
                'drop_custom_tables' => 'Before running the delete tables tool',
                'reset_options' => 'Before running the options reset tool',
                'reset_site' => 'Before running the site reset tool',
            );

            // If tools snapshots is disabled we set this to false as JS will check and skip autosnapshots if it's not object
            if ($options['tools_snapshots'] != 1) {
                $tools_autosnapshot = false;
            }

            $js_localize = array_merge($js_localize, array(
                'support_name' => $support_name,
                'support_text' => $support_text,
                'tools_autosnapshot' => $tools_autosnapshot
            ));
        } else {
            wp_enqueue_style('wp-reset', $this->plugin_url . 'css/wp-reset-global.css', array(), $this->version);
        }

        wp_enqueue_style('wp-reset-sweetalert2', $this->plugin_url . 'css/sweetalert2.min.css', array(), $this->version);
        wp_enqueue_style('wp-reset-select2', $this->plugin_url . 'css/select2.css', array(), $this->version);
        wp_enqueue_style('wp-reset-tooltipster', $this->plugin_url . 'css/tooltipster.bundle.min.css', array(), $this->version);

        wp_enqueue_script('wp-reset-sweetalert2', $this->plugin_url . 'js/wp-reset-libs.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('wp-reset-select2', $this->plugin_url . 'js/select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('wp-reset', $this->plugin_url . 'js/wp-reset.js', array('jquery'), $this->version, true);
        wp_localize_script('wp-reset', 'wp_reset', $js_localize);

        // fix for aggressive plugins that include their CSS on all pages
        if ($this->is_plugin_page()) {
            wp_dequeue_style('uiStyleSheet');
            wp_dequeue_style('wpcufpnAdmin');
            wp_dequeue_style('unifStyleSheet');
            wp_dequeue_style('wpcufpn_codemirror');
            wp_dequeue_style('wpcufpn_codemirrorTheme');
            wp_dequeue_style('collapse-admin-css');
            wp_dequeue_style('jquery-ui-css');
            wp_dequeue_style('tribe-common-admin');
            wp_dequeue_style('file-manager__jquery-ui-css');
            wp_dequeue_style('file-manager__jquery-ui-css-theme');
            wp_dequeue_style('wpmegmaps-jqueryui');
            wp_dequeue_style('wp-botwatch-css');
            wp_dequeue_style('njt-filebird-admin');
            wp_dequeue_style('ihc_jquery-ui.min.css');
            wp_dequeue_style('badgeos-juqery-autocomplete-css');
            wp_dequeue_style('mainwp');
            wp_dequeue_style('mainwp-responsive-layouts');
            wp_dequeue_style('jquery-ui-style');
            wp_dequeue_style('additional_style');
            wp_dequeue_style('wobd-jqueryui-style');
            wp_dequeue_style('wpdp-style3');
            wp_dequeue_style('jquery_smoothness_ui');
            wp_dequeue_style('uap_main_admin_style');
            wp_dequeue_style('uap_font_awesome');
            wp_dequeue_style('uap_jquery-ui.min.css');
        }
    } // admin_enqueue_scripts


    /**
     * Add tools to admin bar
     *
     * @return void
     */
    function admin_bar()
    {
        global $wp_admin_bar, $wp_reset_licensing;

        if (!$wp_reset_licensing->is_active() || !is_admin()) {
            return;
        }

        $options = $this->get_options();

        if (
            !$options['adminbar_snapshots'] ||
            !$wp_reset_licensing->is_active() ||
            false === current_user_can('administrator') ||
            false === apply_filters('wp_reset_show_admin_bar', true)
        ) {
            return;
        }

        $title = '<div class="wpr-adminbar-icon"><img style="height: 22px; padding: 4px; margin-bottom: -10px;" src="' . $this->plugin_url . '/img/wp-reset-icon-small.png" alt="WP Reset" title="WP Reset"></div> <span class="ab-label">WP Reset</span>';

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-reset-ab',
            'title' => $title,
            'href'  => '#',
            'parent' => '',
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-reset',
            'title' => 'Reset',
            'href'  => admin_url('tools.php?page=wp-reset#tab-reset'),
            'parent' => 'wpr-reset-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-tools',
            'title' => 'Tools',
            'href'  => admin_url('tools.php?page=wp-reset#tab-tools'),
            'parent' => 'wpr-reset-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-snapshots',
            'title' => 'Snapshots',
            'href'  => admin_url('tools.php?page=wp-reset#tab-snapshots'),
            'parent' => 'wpr-reset-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-collections',
            'title' => 'Collections',
            'href'  => admin_url('tools.php?page=wp-reset#tab-collections'),
            'parent' => 'wpr-reset-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpr-create-snapshot',
            'title' => 'Create Snapshot',
            'href'  => '#',
            'meta'  => array('class' => 'wpr-admin-bar-create-snapshot'),
            'parent' => 'wpr-reset-ab'
        ));

        /*
    - reset
 - tools
 - snapshots
 */
    } // admin_bar


    /**
     * Remove all WP notices on WPR page
     *
     * @return null
     */
    function remove_admin_notices()
    {
        if (!$this->is_plugin_page()) {
            return false;
        }

        global $wp_filter;
        unset($wp_filter['user_admin_notices'], $wp_filter['admin_notices']);
    } // remove_admin_notices


    /**
     * Add HelpScout Beacon to footer
     *
     * @return null
     */
    function admin_footer()
    {
        if (!$this->is_plugin_page() || !WP_Reset_Utility::whitelabel_filter()) {
            return;
        }

        echo '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>';
    } // admin_footer


    /**
     * Check if WP-CLI is available and running
     *
     * @return bool
     */
    static function is_cli_running()
    {
        if (!is_null($value = apply_filters('wp-reset-override-is-cli-running', null))) {
            return (bool) $value;
        }

        if (defined('WP_CLI') && WP_CLI) {
            return true;
        } else {
            return false;
        }
    } // is_cli_running


    /**
     * Check if core WP Webhooks and WPR addon plugins are installed and activated
     *
     * @return bool
     */
    function is_webhooks_active()
    {
        if (!function_exists('is_plugin_active') || !function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (false == is_plugin_active('wp-webhooks/wp-webhooks.php')) {
            return false;
        }

        if (false == is_plugin_active('wpwh-wp-reset-webhook-integration/wpwhpro-wp-reset-webhook-integration.php')) {
            return false;
        }

        return true;
    } // is_webhooks_active


    /**
     * Check if given plugin is installed
     *
     * @param [string] $slug Plugin slug
     * @return boolean
     */
    function is_plugin_installed($slug)
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        if (!empty($all_plugins[$slug])) {
            return true;
        } else {
            return false;
        }
    } // is_plugin_installed


    /**
     * Deletes all transients.
     *
     * @return int  Number of deleted transient DB entries
     */
    function do_delete_transients()
    {
        global $wpdb;

        do_action('wp_reset_before_delete_transients');

        $count = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'");

        wp_cache_flush();

        do_action('wp_reset_delete_transients', $count);

        return $count;
    } // do_delete_transients


    /**
     * Resets all theme options (mods).
     *
     * @param bool $all_themes Delete mods for all themes or just the current one
     *
     * @return int  Number of deleted mod DB entries
     */
    function do_reset_theme_options($params = array())
    {
        global $wpdb;

        $params = shortcode_atts(array('all_themes' => true), (array) $params);

        do_action('wp_reset_before_reset_theme_options', $params);

        $count = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'theme_mods\_%' OR option_name LIKE 'mods\_%'");

        do_action('wp_reset_reset_theme_options', $count);

        return $count;
    } // do_reset_theme_options


    /**
     * Deletes all files in uploads folder.
     *
     * @return int  Number of deleted files and folders.
     */
    function do_delete_uploads()
    {
        $upload_dir = wp_get_upload_dir();
        $this->delete_count = 0;

        $this->delete_folder($upload_dir['basedir'], $upload_dir['basedir']);

        do_action('wp_reset_delete_uploads', $this->delete_count);

        return $this->delete_count;
    } // do_delete_uploads


    /**
     * Deletes non-default folders in wp-content folder.
     *
     * @return int  Number of deleted files and folders.
     */
    function do_delete_wp_content()
    {
        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);
        $this->delete_count = 0;
        $whitelisted_folders = array('mu-plugins', 'plugins', 'themes', 'uploads', $this->snapshots_folder, $this->autosnapshots_folder);

        $dirs = glob($wp_content_dir . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            if (false == in_array(basename($dir), $whitelisted_folders)) {
                $this->delete_folder($dir, $dir);
                @rmdir($dir);
                $this->delete_count++;
            }
        }

        do_action('wp_reset_delete_wp_content', $this->delete_count, $dirs);

        return $this->delete_count;
    } // do_delete_wp_content


    /**
     * Recursively deletes a folder
     *
     * @param string $folder  Recursive param.
     * @param string $base_folder  Base folder.
     *
     * @return bool
     */
    public function delete_folder($folder, $base_folder)
    {
        if(!file_exists($folder)){
            return true;
        }
        
        $files = array_diff(scandir($folder), array('.', '..'));

        foreach ($files as $file) {
            if (is_dir($folder . DIRECTORY_SEPARATOR . $file)) {
                $this->delete_folder($folder . DIRECTORY_SEPARATOR . $file, $base_folder);
            } else {
                $tmp = @unlink($folder . DIRECTORY_SEPARATOR . $file);
                $this->delete_count += (int) $tmp;
            }
        } // foreach

        if ($folder != $base_folder) {
            $tmp = @rmdir($folder);
            $this->delete_count += (int) $tmp;
            return $tmp;
        } else {
            return true;
        }
    } // delete_folder

    /**
     * Recursively scan folder
     *
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    function scan_folder($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != '.' && $value != '..') {
                $this->scan_folder($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * Recursively copies a folder
     *
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    function copy_folder($source, $destination)
    {
        $dir = opendir($source);
        @mkdir($destination);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    $this->copy_folder($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }
        closedir($dir);
    } // copy_folder


    /**
     * Deactivate all plugins
     *
     * @param array  keep_wp_reset - Keep WP Reset active and installed, silent_deactivate - Skip individual plugin deactivation functions when deactivating
     *
     * @return int  Number of deactivated plugins.
     */
    function do_deactivate_plugins($params = array())
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $wp_reset_basename = plugin_basename(WP_RESET_FILE);
        $params = shortcode_atts(array('keep_wp_reset' => true, 'silent_deactivate' => false), (array) $params);

        $active_plugins = (array) get_option('active_plugins', array());
        if ($params['keep_wp_reset']) {
            if (($key = array_search($wp_reset_basename, $active_plugins)) !== false) {
                unset($active_plugins[$key]);
            }
        }

        if (!empty($active_plugins)) {
            deactivate_plugins($active_plugins, $params['silent_deactivate'], false);
        }

        do_action('wp_reset_deactivate_plugins', $active_plugins, $params);

        return sizeof($active_plugins);
    } // do_delete_plugins


    /**
     * Delete all plugins
     *
     * @param array  keep_wp_reset - Keep WP Reset active and installed
     *
     * @return int  Number of deleted plugins.
     */
    function do_delete_plugins($params = array())
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $wp_reset_basename = plugin_basename(WP_RESET_FILE);
        $params = shortcode_atts(array('keep_wp_reset' => true), (array) $params);

        $all_plugins = get_plugins();
        if ($params['keep_wp_reset']) {
            unset($all_plugins[$wp_reset_basename]);
        }

        if (!empty($all_plugins)) {
            delete_plugins(array_keys($all_plugins));
        }

        do_action('wp_reset_delete_plugins', $all_plugins, $params);

        return sizeof($all_plugins);
    } // do_delete_plugins


    /**
     * Delete all themes
     *
     * @param array  $params keep_default_theme -  Keep default theme
     *
     * @return int  Number of deleted themes.
     */
    function do_delete_themes($params)
    {
        global $wp_version;

        if (!function_exists('delete_theme')) {
            require_once ABSPATH . 'wp-admin/includes/theme.php';
        }

        if (!function_exists('request_filesystem_credentials')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $current_theme = wp_get_theme();
        $params = shortcode_atts(array('keep_default_theme' => false, 'keep_current_theme' => false), (array) $params);

        if (version_compare($wp_version, '5.3', '>=') === true) {
            $default_theme = 'twentytwenty';
        } elseif (version_compare($wp_version, '5.0', '>=') === true) {
            $default_theme = 'twentynineteen';
        } else {
            $default_theme = 'twentyseventeen';
        }

        $all_themes = wp_get_themes(array('errors' => null));

        if ($params['keep_default_theme']) {
            unset($all_themes[$default_theme]);
        }

        if ($params['keep_current_theme'] && is_object($current_theme)) {
            $parent_theme = $current_theme->parent();
            if ($parent_theme != false) {
                unset($all_themes[$parent_theme->get_stylesheet()]);
            }
            unset($all_themes[$current_theme->get_stylesheet()]);
        }

        foreach ($all_themes as $theme_slug => $theme_details) {
            delete_theme($theme_slug);
        }

        if (!$params['keep_default_theme'] && !$params['keep_current_theme']) {
            update_option('template', '');
            update_option('stylesheet', '');
            update_option('current_theme', '');
        }

        do_action('wp_reset_delete_themes', $all_themes, $params);

        return sizeof($all_themes);
    } // do_delete_themes


    /**
     * Truncate custom tables
     *
     * @return int  Number of truncated tables.
     */
    function do_truncate_custom_tables($params)
    {
        global $wpdb;
        $cnt = 0;

        $custom_tables = $this->get_custom_tables();

        $params = shortcode_atts(array('tables' => array()), (array) $params);

        if (empty($params['tables'])) {
            do_action('wp_reset_truncate_custom_tables', $custom_tables, $params, 0);
            return 0;
        }

        do_action('wp_reset_before_truncate_custom_tables', $params);

        foreach ($custom_tables as $tbl) {
            if (in_array($tbl['name'], $params['tables']) || in_array('__all', $params['tables'])) {
                $wpdb->query('SET foreign_key_checks = 0');
                $wpdb->query('TRUNCATE TABLE `' . $tbl['name'] . '`');
                $cnt++;
            }
        } // foreach

        do_action('wp_reset_truncate_custom_tables', $custom_tables, $params, $cnt);

        return $cnt;
    } // do_truncate_custom_tables


    /**
     * Drop custom tables
     *
     * @return int  Number of dropped tables.
     */
    function do_drop_custom_tables($params)
    {
        global $wpdb;
        $cnt = 0;
        $custom_tables = $this->get_custom_tables();

        $params = shortcode_atts(array('tables' => array()), (array) $params);

        if (empty($params['tables'])) {
            do_action('wp_reset_drop_custom_tables', $custom_tables, $params, 0);
            return 0;
        }

        do_action('wp_reset_before_drop_custom_tables', $params);

        foreach ($custom_tables as $tbl) {
            if (in_array($tbl['name'], $params['tables']) || in_array('__all', $params['tables'])) {
                $wpdb->query('SET foreign_key_checks = 0');
                $wpdb->query('DROP TABLE IF EXISTS `' . $tbl['name'] . '`');
                $cnt++;
            }
        } // foreach

        do_action('wp_reset_drop_custom_tables', $custom_tables, $params, $cnt);

        return $cnt;
    } // do_drop_custom_tables


    /**
     * Delete .htaccess file
     *
     * @return bool|WP_Error Action status.
     */
    function do_delete_htaccess()
    {
        global $wp_filesystem;

        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        $htaccess_path = $this->get_htaccess_path();
        clearstatcache();

        do_action('wp_reset_delete_htaccess', $htaccess_path);

        if (!$wp_filesystem->is_readable($htaccess_path)) {
            return new WP_Error(1, 'Htaccess file does not exist; there\'s nothing to delete.');
        }

        if (!$wp_filesystem->is_writable($htaccess_path)) {
            return new WP_Error(1, 'Htaccess file is not writable.');
        }

        if ($wp_filesystem->delete($htaccess_path, false, 'f')) {
            return true;
        } else {
            return new WP_Error(1, 'Unknown error. Unable to delete htaccess file.');
        }
    } // do_delete_htaccess


    /**
     * Reset user roles
     *
     * @return bool|WP_Error Action status.
     */
    function do_reset_user_roles()
    {
        global $wp_roles, $wp_user_roles;

        $wp_user_roles = null;
        $wp_roles->roles = array();
        $wp_roles->role_objects = array();
        $wp_roles->role_names = array();
        $wp_roles->use_db = true;

        require_once(ABSPATH . '/wp-admin/includes/schema.php');
        populate_roles();
        $wp_roles = new WP_Roles();

        return true;
    } // do_reset_user_roles


    /**
     * Restore .htaccess file
     *
     * @return bool|WP_Error Action status.
     */
    function do_restore_htaccess()
    {
        global $wp_filesystem;

        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        $htaccess_path = $this->get_htaccess_path();
        clearstatcache();

        do_action('wp_reset_restore_htaccess', $htaccess_path);

        if ($wp_filesystem->delete($htaccess_path, false, 'f')) {
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            if (save_mod_rewrite_rules()) {
                return true;
            } else {
                return new WP_Error(1, 'Unknown error. Unable to restore htaccess file.');
            }
        } else {
            return new WP_Error(1, 'Unknown error. Unable to delete htaccess file.');
        }
    } // do_delete_htaccess


    /**
     * Get .htaccess file path.
     *
     * @return string
     */
    function get_htaccess_path()
    {
        if (!function_exists('get_home_path')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        if ($this->is_cli_running()) {
            $_SERVER['SCRIPT_FILENAME'] = ABSPATH;
        }

        $filepath = get_home_path() . '.htaccess';

        return $filepath;
    } // get_htaccess_path


    function search_wporg($search, $themes = false)
    {
        $results = array();

        if ($themes) {
            $find = 'themes';
        } else {
            $find = 'plugins';
        }

        $request_params = array('sslverify' => false, 'timeout' => 25, 'redirection' => 2);
        $url = 'https://api.wordpress.org/' . $find . '/info/1.2/?action=query_' . $find . '&request[page]=1&request[per_page]=20&request[locale]=en_US&request[search]=' . $search . '&request[wp_version]=5.2&request[fields][short_description]=0&request[fields][description]=0&request[fields][tested]=0&request[fields][requires]=0&request[fields][rating]=0&request[fields][ratings]=0&request[fields][downloaded]=0&request[fields][downloadlink]=0&request[fields][last_updated]=0&request[fields][ratings]=0&request[fields][added]=0&request[fields][tags]=0&request[fields][compatibility]=0&request[fields][homepage]=0&request[fields][versions]=0&request[fields][donate_link]=0&request[fields][reviews]=0&request[fields][group]=0&request[fields][contributors]=0&request[fields][active_installs]=0&request[fields][icons]=0&request[fields][author_profile]=0&request[fields][requires_php]=0&request[fields][num_ratings]=0&request[fields][support_threads]=0&request[fields][support_threads_resolved]=0&request[fields][download_link]=0';
        $response = wp_remote_get($url, $request_params);
        $body = wp_remote_retrieve_body($response);
        $wp_search_results = @json_decode($body, true);

        if (is_wp_error($response) || empty($body) || !is_array($wp_search_results)) {
            return array();
        } else {
            foreach ($wp_search_results[$find] as $wp_result) {
                $results[] = array('id' => $wp_result['slug'], 'text' => html_entity_decode($wp_result['name']));
            }
        }

        return $results;
    }

    /**
     * Run one tool via AJAX call
     *
     * @return null
     */
    function ajax_run_tool()
    {
        check_ajax_referer('wp-reset_run_tool');
        if (!current_user_can('administrator')) {
            wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
        }

        if (!class_exists('ZipArchive')) {
            wp_send_json_error(__('The PHP ZipArchive Library is missing or disabled! Please contact your host to enable it before running WP Reset tools', 'wp-reset'));
        }

        global $wp_reset_tools, $wp_reset_collections, $wp_reset_cloud, $wp_reset_licensing;
        $options = $this->get_options();
        $tool = trim(@$_REQUEST['tool']);
        $extra_data = @$_REQUEST['extra_data'];
        if (is_string($extra_data)) {
            $extra_data = trim($extra_data);
            $extra_data = substr($extra_data, 0, 255);
        } elseif (is_array($extra_data)) {
            $extra_data = array_slice($extra_data, 0, 20);
        } else {
            $extra_data = false;
        }

        if ($options['debug']) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        }


        if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
            @set_time_limit(900);
        }

        if ($options['throttle_ajax'] == true) {
            usleep(1000000);
        }

        if ($tool == 'test') {
            sleep(rand(0, 2));
            if (rand(0, 10) % 2) {
                wp_send_json_error('Error message example.');
            } else {
                wp_send_json_success(rand(0, 100));
            }
        } elseif ($tool == 'reload_collections') {
            $res = $wp_reset_collections->reload_collections();
            if (is_wp_error($res)) {
                $this->log('error', 'Error refreshing collections: ' . $res->get_error_message());
                wp_send_json_error($res->get_error_message());
            } else {
                $this->log('info', 'Collections refreshed sucessfully');
                wp_send_json_success();
            }
        } elseif ($tool == 'wp_slug_search') {
            $search = false;
            if (!empty($extra_data['search'])) {
                $search = $extra_data['search'];
            }
            wp_send_json_success($this->search_wporg($search, filter_var($extra_data['theme'], FILTER_VALIDATE_BOOLEAN)));
        } elseif ($tool == 'add_new_collection') {
            $res = $wp_reset_collections->add_new_collection($extra_data);
            if (is_wp_error($res)) {
                $this->log('error', 'Error creating ' . $extra_data['name'] . ' collection: ' . $res->get_error_message());
                wp_send_json_error($res->get_error_message());
            } else {
                $tmp = $wp_reset_collections->get_collection_card($res);
                $this->log('info', 'Collection ' . $extra_data['name'] . ' created sucessfully');
                wp_send_json_success($tmp);
            }
        } elseif ($tool == 'add_collection_item') {
            $extra_data = @$_REQUEST;
            $res = $wp_reset_collections->add_collection_item($extra_data);
            if (is_wp_error($res)) {
                $this->log('error', 'Error adding ' . $extra_data['slug'] . ' to collection: ' . $res->get_error_message());
                wp_send_json_error($res->get_error_message());
            } else {
                $tmp = $wp_reset_collections->get_collection_item($res);
                $this->log('info', 'Collection item ' . $extra_data['slug'] . ' added to collection sucessfully');
                wp_send_json_success(array('item' => $tmp, 'collections' => $wp_reset_collections->get_collections_keyed()));
            }
        } elseif ($tool == 'edit_collection_item') {
            $extra_data = @$_REQUEST;
            $res = $wp_reset_collections->edit_collection_item($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                $tmp = $wp_reset_collections->get_collection_item($res);
                wp_send_json_success(array('item' => $tmp, 'collections' => $wp_reset_collections->get_collections_keyed()));
            }
        } elseif ($tool == 'delete_collection') {
            $res = $wp_reset_collections->delete_collection($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } elseif ($tool == 'delete_collection_item') {
            $res = $wp_reset_collections->delete_collection_item($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success(array('collections' => $wp_reset_collections->get_collections_keyed()));
            }
        } elseif ($tool == 'edit_collection_name') {
            $res = $wp_reset_collections->edit_collection_name($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'edit_snapshot_name') {
            $res = $wp_reset_tools->edit_snapshot_name($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'save_snapshot_options') {
            $res = $wp_reset_tools->save_snapshot_options($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } elseif ($tool == 'site_reset') {
            $res = $this->do_reinstall($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'delete_transients') {
            $cnt = $this->do_delete_transients();
            wp_send_json_success($cnt);
        } elseif ($tool == 'purge_cache') {
            $wp_reset_tools->do_purge_cache();
            wp_send_json_success();
        } elseif ($tool == 'delete_widgets') {
            $cnt = $wp_reset_tools->do_delete_widgets();
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_wp_cookies') {
            wp_clear_auth_cookie();
            wp_send_json_success();
        } elseif ($tool == 'reset_theme_options') {
            $cnt = $this->do_reset_theme_options(array('all_themes' => true));
            wp_send_json_success($cnt);
        } elseif ($tool == 'reset_options') {
            $res = $wp_reset_tools->do_reset_options($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success(true);
            }
        } elseif ($tool == 'delete_content') {
            $res = $wp_reset_tools->do_delete_content($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'delete_themes') {
            $cnt = $this->do_delete_themes($extra_data);
            wp_send_json_success($cnt);
        } elseif ($tool == 'deactivate_plugins') {
            $cnt = $this->do_deactivate_plugins($extra_data);
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_plugins') {
            $cnt = $this->do_delete_plugins($extra_data);
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_uploads') {
            $cnt = $this->do_delete_uploads();
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_wp_content') {
            $cnt = $this->do_delete_wp_content();
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_htaccess') {
            $tmp = $this->do_delete_htaccess();
            if (is_wp_error($tmp)) {
                wp_send_json_error($tmp->get_error_message());
            } else {
                wp_send_json_success($tmp);
            }
        } elseif ($tool == 'restore_htaccess') {
            $tmp = $this->do_restore_htaccess();
            if (is_wp_error($tmp)) {
                wp_send_json_error($tmp->get_error_message());
            } else {
                wp_send_json_success($tmp);
            }
        } elseif ($tool == 'reset_user_roles') {
            $tmp = $this->do_reset_user_roles();
            if (is_wp_error($tmp)) {
                wp_send_json_error($tmp->get_error_message());
            } else {
                wp_send_json_success($tmp);
            }
        } elseif ($tool == 'drop_custom_tables') {
            $cnt = $this->do_drop_custom_tables($extra_data);
            wp_send_json_success($cnt);
        } elseif ($tool == 'truncate_custom_tables') {
            $cnt = $this->do_truncate_custom_tables($extra_data);
            wp_send_json_success($cnt);
        } elseif ($tool == 'delete_mu_plugins') {
            $cnt = $wp_reset_tools->do_delete_mu_plugins($extra_data);
            if (is_wp_error($cnt)) {
                wp_send_json_error($cnt->get_error_message());
            } else {
                wp_send_json_success($cnt);
            }
        } elseif ($tool == 'delete_dropins') {
            $cnt = $wp_reset_tools->do_delete_dropins($extra_data);
            if (is_wp_error($cnt)) {
                wp_send_json_error($cnt->get_error_message());
            } else {
                wp_send_json_success($cnt);
            }
        } elseif ($tool == 'delete_snapshot') {
            $res = $this->do_delete_snapshot($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } elseif ($tool == 'delete_snapshots') {
            $cnt = $this->do_delete_snapshots($extra_data);
            if (is_wp_error($cnt)) {
                wp_send_json_error($cnt->get_error_message());
            } else {
                wp_send_json_success($cnt);
            }
        } elseif ($tool == 'download_snapshot') {
            $res = $this->do_export_snapshot($extra_data['uid']);

            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else if (is_array($res) && $res['ajax'] === true) {
                $steps = array();
                foreach ($res['tbl_names'] as $table) {
                    $steps[] = array('uid' => $res['uid'], 'action' => 'export', 'data' => $table, 'description' => 'Exporting table <i>' . $table . '</i>');
                }
                $steps[] = array('uid' => $res['uid'], 'action' => 'verify_export', 'data' => false, 'description' => 'Verifying exported snapshot');
                wp_send_json_success($steps);
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'export_snapshot_step') {
            $res = $this->export_snapshot_step($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'restore_snapshot') {
            $res = $this->do_restore_snapshot($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } elseif ($tool == 'compare_snapshots') {
            $res = $this->do_compare_snapshots($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'cloud_action') {
            $res = $wp_reset_cloud->cloud_action($extra_data);

            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'create_snapshot') {
            $res = $this->do_create_snapshot($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else if ($res['ajax'] == true) {
                $steps = array();
                foreach ($res['tbl_names'] as $table) {
                    $steps[] = array('uid' => $res['uid'], 'action' => 'copy', 'data' => $table, 'description' => 'Copying table <i>' . $table . '</i>');
                }
                $steps[] = array('uid' => $res['uid'], 'action' => 'verify_integrity', 'data' => false, 'description' => 'Verifying snapshot');
                wp_send_json_success($steps);
            } else {
                $tmp = $this->get_snapshot_row($res, 'user');
                wp_send_json_success(array('html' => $tmp, 'uid' => $res['uid']));
            }
        } elseif ($tool == 'create_snapshot_step') {
            $res = $this->create_snapshot_step($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                $tmp = $this->get_snapshot_row($res, 'user');
                wp_send_json_success(array('html' => $tmp, 'uid' => $res['uid'], 'auto' => $res['auto']));
            }
        } elseif ($tool == 'get_table_details') {
            $res = WP_Reset_Utility::get_table_details();
            wp_send_json_success($res);
        } elseif ($tool == 'onboarding_done') {
            $options['onboarding_done'] = true;
            $this->update_options('options', $options);
        } elseif ($tool == 'test_snapshot') {
            $start_time = microtime(true);
            $snapshot = array(
                'name' => 'Onboarding Test',
                'auto' => true,
                'plugins' => array(
                    'WP Reset PRO' => plugin_dir_path(WP_PLUGIN_DIR . '/wp-reset/wp-reset.php')
                )
            );

            $res = $this->do_create_snapshot($snapshot);
            if (is_wp_error($res)) {
                $options['tools_snapshots'] = false;
                $options['events_snapshots'] = false;
                $options['snapshots_autoupload'] = false;
                $options['autosnapshots_autoupload'] = false;
                $options['onboarding_done'] = true;
                $this->update_options('options', $options);
                wp_send_json_error($res->get_error_message());
            } else {
                $this->do_delete_snapshot($res['uid']);
                $duration = round((microtime(true) - $start_time) * 100) / 100;
                if ($duration < 10) {
                    $options['tools_snapshots'] = true;
                    $options['events_snapshots'] = true;
                } else {
                    $options['tools_snapshots'] = false;
                    $options['events_snapshots'] = false;
                }
                $options['onboarding_done'] = true;
                $this->update_options('options', $options);
                wp_send_json_success(array('auto' => $options['tools_snapshots']));
            }
        } elseif ($tool == 'install_recovery') {
            $res = $this->do_install_recovery();
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'uninstall_recovery') {
            $res = $this->do_uninstall_recovery();
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'update_recovery') {
            $res = $this->do_update_recovery();
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'email_recovery') {
            $res = $this->send_recovery_script_email($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'change_single_option') {
            $options[$extra_data['option']] = filter_var($extra_data['value'], FILTER_VALIDATE_BOOLEAN);
            $this->update_options('options', $options);
        } elseif ($tool == 'switch_wp_version') {
            $res = $wp_reset_tools->do_switch_wp_version($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif ($tool == 'refresh_wp_versions') {
            if ($wp_reset_tools->get_wordpress_versions(true) !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error(__('WordPress list could not be refreshed! Reload the page and try again.', 'wp-reset'));
            }
        } elseif ($tool == 'import_snapshot') {
            if (!empty($_FILES['snapshot_zip']) && $_FILES['snapshot_zip']['error'] == 0) {
                $import_folder = $this->export_dir_path();
                if (is_wp_error($import_folder)) {
                    wp_send_json_error($import_folder->get_error_message());
                }
                $import_file = $import_folder . '/' . $_FILES['snapshot_zip']['name'];

                move_uploaded_file($_FILES['snapshot_zip']['tmp_name'], $import_file);

                $import_res = $this->do_import_snapshot($import_file, true);

                if (is_wp_error($import_res)) {
                    wp_send_json_error($import_res->get_error_message());
                } else {
                    wp_send_json_success($import_res);
                }
            } else {
                wp_send_json_error(__('File upload error!', 'wp-reset'));
            }
        } elseif ($tool == 'import_snapshot_step') {
            $res = $this->import_snapshot_step($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } elseif (
            $tool == 'check_delete_theme' ||
            $tool == 'check_install_theme' ||
            $tool == 'check_activate_theme'
        ) {

            $theme = wp_get_theme($_GET['slug']);
            if ($theme->exists()) {
                $current_theme = wp_get_theme();
                if ($theme->get_stylesheet() == $current_theme->get_stylesheet()) {
                    wp_send_json_success('active');
                } else {
                    wp_send_json_success('inactive');
                }
            } else {
                wp_send_json_success('deleted');
            }
        } elseif ($tool == 'install_theme') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $slug = $_GET['slug'];

            if (isset($_GET['extra_data'])) {
                $source = $_GET['extra_data']['source'];
            } else {
                $source = 'repo';
            }

            @include_once ABSPATH . 'wp-admin/includes/theme.php';
            @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            @include_once ABSPATH . 'wp-admin/includes/file.php';
            @include_once ABSPATH . 'wp-admin/includes/misc.php';

            wp_cache_flush();

            $theme = wp_get_theme($slug);

            if ($theme->exists()) {
                // Theme is already installed
                wp_send_json_success();
            } else {
                // Install Theme
                $skin      = new WP_Ajax_Upgrader_Skin();
                $upgrader  = new Theme_Upgrader($skin);

                if ($source == 'zip') {
                    $collection_item = $wp_reset_collections->get_collection_item_details($_GET['extra_data']['collection_id'], $_GET['extra_data']['collection_item_id']);
                    if ($collection_item['location'] == 'wpreset') {
                        $upgrader->install(trailingslashit($wp_reset_cloud->cloud_url) . $collection_item['zip_url']);
                    } else if ($collection_item['location'] == 'pcloud' || $collection_item['location'] == 'pcloudeu') {
                        global $wp_reset_cloud;

                        if (($options['cloud_service'] != 'pcloud' && $options['cloud_service'] != 'pcloudeu') || is_wp_error($wp_reset_cloud->get_pcloud_client())) {
                            set_transient('wf_install_error_' . $slug, 'You need to connect to the pCloud account where this collection item is stored in order to install it.', 300);
                        }

                        try {
                            $pcloudFile = new pCloud\File();
                            $download_result = $pcloudFile->download((int)$collection_item['zip_filename'], $this->export_dir_path());
                            $url = $this->export_dir_path(basename($download_result), true);
                        } catch (Exception $e) {
                            set_transient('wf_install_error_' . $slug, 'This collection item was not found in the current pCloud account. Please connect to the pCloud account you used when adding it. ' . $e->getMessage(), 300);
                        }

                        $upgrader->install($url);
                    }  else if ($collection_item['location'] == 'icedrive') {
                        global $wp_reset_cloud;

                        if ($options['cloud_service'] != 'icedrive' || is_wp_error($wp_reset_cloud->get_icedrive_client())) {
                            set_transient('wf_install_error_' . $slug, 'You need to connect to the Icedrive account where this collection item is stored in order to install it.', 300);
                        }

                        try {
                            $icedriveFile = $this->icedrive->request('GET', $collection_item['zip_filename']);
                            file_put_contents($wp_reset->export_dir_path(basename($collection_item['zip_filename'])), $icedriveFile['body']);
                            $download_checksum = $this->file_checksum($wp_reset->export_dir_path(basename($collection_item['zip_filename'])));

                            $url = $this->export_dir_path(basename($collection_item['zip_filename']), true);
                        } catch (Exception $e) {
                            set_transient('wf_install_error_' . $slug, 'This collection item was not found in the current Icedrive account. Please connect to the Icedrive account you used when adding it. ' . $e->getMessage(), 300);
                        }

                        $upgrader->install($url);
                    } else {
                        $upgrader->install($collection_item['zip_url']);
                    }
                } else {
                    $upgrader->install('https://downloads.wordpress.org/theme/' . $slug . '.latest-stable.zip');
                }

                wp_send_json_success();
            }
        } elseif ($tool == 'activate_theme') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $stylesheet = $_GET['slug'];
            $theme = wp_get_theme($stylesheet);
            if ($theme->exists()) {
                switch_theme($stylesheet);
            }
            wp_send_json_success();
        } elseif ($tool == 'delete_theme') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $stylesheet = $_GET['slug'];
            $theme = wp_get_theme($stylesheet);
            if ($theme->exists()) {
                $res = delete_theme($stylesheet);
            }
            wp_send_json_success();
        } elseif (
            $tool == 'check_deactivate_plugin' ||
            $tool == 'check_delete_plugin' ||
            $tool == 'check_install_plugin' ||
            $tool == 'check_activate_plugin'
        ) {
            $path = $this->get_plugin_path($_GET['slug']);
            if (false !== ($error = get_transient('wf_install_error_' . $_GET['slug']))) {
                delete_transient('wf_install_error_' . $_GET['slug']);
                wp_send_json_success($error);
            }

            if (false !== $path) {
                $active_plugins = (array) get_option('active_plugins', array());
                if (false !== array_search($path, $active_plugins)) {
                    wp_send_json_success('active');
                } else {
                    wp_send_json_success('inactive');
                }
            } else {
                wp_send_json_success('deleted');
            }
        } elseif ($tool == 'install_plugin') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $slug = $_GET['slug'];

            if (isset($_GET['extra_data'])) {
                $source = $_GET['extra_data']['source'];
            } else {
                $source = 'repo';
            }

            @include_once ABSPATH . 'wp-admin/includes/plugin.php';
            @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            @include_once ABSPATH . 'wp-admin/includes/file.php';
            @include_once ABSPATH . 'wp-admin/includes/misc.php';

            wp_cache_flush();

            $path = $this->get_plugin_path($slug);

            if (false !== $path) {
                // Plugin is already installed
                wp_send_json_success();
            } else {
                // Install Plugin

                $skin      = new WP_Ajax_Upgrader_Skin();
                $upgrader = new Plugin_Upgrader($skin);

                if ($source == 'zip') {
                    $collection_item = $wp_reset_collections->get_collection_item_details($_GET['extra_data']['collection_id'], $_GET['extra_data']['collection_item_id']);

                    if ($collection_item['location'] == 'wpreset') {
                        $upgrader->install(trailingslashit($wp_reset_cloud->cloud_url) . $collection_item['zip_url']);
                    } else if ($collection_item['location'] == 'pcloud' || $collection_item['location'] == 'pcloudeu') {
                        global $wp_reset_cloud;

                        if (($options['cloud_service'] != 'pcloud' && $options['cloud_service'] != 'pcloudeu') || is_wp_error($wp_reset_cloud->get_pcloud_client())) {
                            set_transient('wf_install_error_' . $slug, 'You need to connect to the pCloud account where this collection item is stored in order to install it.', 300);
                        }

                        try {
                            $pcloudFile = new pCloud\File();
                            $download_result = $pcloudFile->download((int)$collection_item['zip_filename'], $this->export_dir_path());
                            $url = $this->export_dir_path(basename($download_result), true);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            set_transient('wf_install_error_' . $slug, 'This collection item was not found in the current pCloud account. Please connect to the pCloud account you used when adding it. ' . $e->getMessage(), 300);
                        }

                        $upgrader->install($url);
                    } else if ($collection_item['location'] == 'icedrive') {
                        global $wp_reset_cloud;

                        if ($options['cloud_service'] != 'icedrive' || is_wp_error($wp_reset_cloud->get_pcloud_client())) {
                            set_transient('wf_install_error_' . $slug, 'You need to connect to the Icedrive account where this collection item is stored in order to install it.', 300);
                        }

                        try {
                            $icedrive_client = $wp_reset_cloud->get_icedrive_client();
                            $icedriveFile = $icedrive_client->request('GET', $collection_item['zip_filename']);
                            file_put_contents($this->export_dir_path(basename($collection_item['zip_filename'])), $icedriveFile['body']);
                            
                            $url = $this->export_dir_path(basename($collection_item['zip_filename']), true);
                        } catch (Exception $e) {
                            set_transient('wf_install_error_' . $slug, 'This collection item was not found in the current Icedrive account. Please connect to the Icedrive account you used when adding it. ' . $e->getMessage(), 300);
                        }

                        $upgrader->install($url);
                    } else {
                        $upgrader->install($collection_item['zip_url']);
                    }
                } else {
                    $upgrader->install('https://downloads.wordpress.org/plugin/' . $slug . '.latest-stable.zip');
                }

                wp_send_json_success();
            }
        } elseif ($tool == 'activate_plugin') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            if (isset($_GET['extra_data'])) {
                $source = $_GET['extra_data']['source'];
            } else {
                $source = 'repo';
            }

            $path = $this->get_plugin_path($_GET['slug']);
            activate_plugin($path);

            wp_send_json_success();
        } elseif (
            $tool == 'activate_license_plugin' ||
            $tool == 'activate_license_theme'
        ) {
            $result = $wp_reset_collections->license_activation($_GET['slug'], $_GET['extra_data']['license_key']);
            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            } else {
                if ($result !== false) {
                    wp_send_json_success($result);
                }
            }
            wp_send_json_error('unknown');
        } elseif (
            $tool == 'check_activate_license_plugin' ||
            $tool == 'check_activate_license_theme'
        ) {
            $result = $wp_reset_collections->check_license_activation($_GET['slug']);
            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            } else {
                if ($result !== false) {
                    wp_send_json_success($result);
                }
            }
            wp_send_json_error('unknown');
        } elseif ($tool == 'deactivate_plugin') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $path = $this->get_plugin_path($_GET['slug']);
            if ($path !== false) {
                deactivate_plugins($path, false, false);
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        } elseif ($tool == 'delete_plugin') {
            $GLOBALS['wpr_autosnapshot_done'] = true;

            $path = $this->get_plugin_path($_GET['slug']);
            if ($path !== false) {
                delete_plugins(array($path));
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        } else if ($tool == 'autouploader_step') {
            wp_send_json_success($this->autosnapshots_uploader_step());
        } else if ($tool == 'autouploader_status') {
            global $wp_reset_cloud;
            $autouploader = $this->get_autouploader();
            $snapshots = $this->get_snapshots();
            $cloud_snapshots = $wp_reset_cloud->get_cloud_snapshots();
            $statuses = array();

            foreach ($autouploader['snapshots'] as $uid => $snapshot) {
                $statuses[$uid] = array(
                    'status' => $snapshot['status'],
                    'message' => @$snapshot['message']
                );
            }

            foreach ($cloud_snapshots as $uid => $snapshot) {
                $statuses[$uid] = array(
                    'status' => 'finished',
                    'message' => 'Finished uploading'
                );
            }
            wp_send_json_success($statuses);
        } else {
            wp_send_json_error(__('Unknown tool.', 'wp-reset'));
        }
    } // ajax_run_tool

    function autosnapshots_uploader_reset()
    {
        $autouploader = array('snapshots' => array(), 'current_snapshot' => false, 'status' => 'waiting', 'message' => 'Waiting for new autosnapshots');
        $this->update_options('autouploader', $autouploader);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        }

        return true;
    }

    function autosnapshots_uploader_step()
    {
        global $wp_reset_cloud;

        $options = $this->get_options();
        $snapshots = $this->get_snapshots();
        $autouploader = $this->get_autouploader();

        $return_message = '';
        //$autouploader = array('snapshots' => array(), 'current_snapshot' => false);
        $cloud_snapshots = $wp_reset_cloud->get_cloud_snapshots();

        if (array_key_exists('started', $autouploader) && $autouploader['started'] > 0) {
            if ($autouploader['started'] + 300 > time()) {
                return array('uid' => $autouploader['current_snapshot'], 'status' => $autouploader['status'], 'message' => $autouploader['message']);
            } else {
                $autouploader['status'] = 'error';
                if ($autouploader['current_snapshot'] != false) {
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'error';
                }
                $autouploader['message'] = 'Unknow error occured' . (isset($autouploader['snapshots'][$autouploader['current_snapshot']]['message']) ? ' last message was: ' . $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] : '');
                $autouploader['current_snapshot'] = false;
                $autouploader['started'] = false;
                $this->update_options('autouploader', $autouploader);
                return array('uid' => $autouploader['current_snapshot'], 'status' => 'error', 'message' => $autouploader['message']);
            }
        }

        if(!array_key_exists('snapshots', $autouploader)) {
            $autouploader['snapshots'] = array();
        }
        
        foreach ($snapshots as $snapshot) {
            if (array_key_exists('autoupload', $snapshot) && $snapshot['autoupload'] == true) {
                if (!array_key_exists($snapshot['uid'], $autouploader['snapshots']) && !array_key_exists($snapshot['uid'], $cloud_snapshots)) {
                    $this->log('error', 'Autouploader: Add snapshot ID ' . $snapshot['uid'] . ' to queue');
                    $autouploader['snapshots'][$snapshot['uid']] = array(
                        'steps' => array(),
                        'status' => 'pending',
                        'message' => 'Pending upload'
                    );
                }
            }
        }

        foreach ($autouploader['snapshots'] as $uid => $snapshot) {
            if (!array_key_exists($uid, $snapshots)) {
                unset($autouploader['snapshots'][$uid]);
            }
        }
        $this->update_options('autouploader', $autouploader);

        if (count($autouploader['snapshots']) == 0) {
            $autouploader = array('snapshots' => array(), 'current_snapshot' => false, 'status' => 'waiting', 'message' => 'Waiting for new autosnapshots');
            $this->update_options('autouploader', $autouploader);
            $this->log('info', 'Autouploader: No snapshots pending');
            return false;
        }


        if ($autouploader['current_snapshot'] == false || !array_key_exists($autouploader['current_snapshot'], $autouploader['snapshots']) || $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] == 'pending' || $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] == 'error' || !array_key_exists($autouploader['current_snapshot'], $autouploader['snapshots'])) {
            $autouploader['current_snapshot'] = false;
            foreach ($autouploader['snapshots'] as $uid => $snapshot) {
                if ($snapshot['status'] != 'error') {
                    $autouploader['current_snapshot'] = $uid;
                    break;
                }
            }

            if ($autouploader['current_snapshot'] == false) {
                return;
            }

            $this->log('info', 'Autouploader: New snapshot to process ' . $autouploader['current_snapshot']);

            $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'exporting';
            $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'] = array();
            $autouploader['snapshots'][$autouploader['current_snapshot']]['current_step'] = false;

            $autouploader['status'] = 'exporting';
            $autouploader['message'] = 'Preparing to export snapshot';
            $autouploader['started'] = time();
            $this->update_options('autouploader', $autouploader);
            $res = $this->do_export_snapshot($autouploader['current_snapshot'], true);

            if (is_wp_error($res)) {
                $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'error';
                $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = $res->get_error_message();
                $autouploader['status'] = 'error';
                $autouploader['message'] = 'Error preparing snapshot <strong>' . $autouploader['current_snapshot'] . '</strong> for export: ' . $res->get_error_message();
            } else {
                foreach ($res['tbl_names'] as $table) {
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][] = array('uid' => $res['uid'], 'action' => 'export', 'data' => $table, 'description' => 'Exporting table <i>' . $table . '</i>');
                }
                $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][] = array('uid' => $res['uid'], 'action' => 'verify_export', 'data' => false, 'description' => 'Verifying exported snapshot');
            }
        }

        if ($autouploader['current_snapshot'] == false) {
            $this->log('info', 'Autouploader: No snapshot to autoupload');
            return false;
        }

        if ($autouploader['snapshots'][$autouploader['current_snapshot']]['status'] == 'exporting') {
            //Get current step
            if (count($autouploader['snapshots'][$autouploader['current_snapshot']]['steps']) > 0) {
                $current_step = $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0];
                switch ($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['action']) {
                    case 'export':
                        $autouploader['status'] = 'exporting';
                        $autouploader['message'] = 'Exporting table ' . $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['data'];
                        $this->log('info', 'Autouploader: Exporting table ' . $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['data']);
                        break;
                    case 'verify_export':
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'exporting';
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Verifying exported file';
                        $this->log('info', 'Autouploader: Verifying exported file');
                        break;
                }

                $autouploader['started'] = time();
                $this->update_options('autouploader', $autouploader);
                $res = $this->export_snapshot_step($current_step);
                $autouploader['started'] = false;

                if (is_wp_error($res)) {
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'error';
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = $res->get_error_message();
                } else {
                    switch ($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['action']) {
                        case 'export':
                            $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'exporting';
                            $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Exported table ' . $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['data'];
                            break;
                        case 'verify_export':
                            $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'exporting';
                            $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Verified exported file';
                            break;
                    }
                    unset($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]);
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'] = array_values($autouploader['snapshots'][$autouploader['current_snapshot']]['steps']);
                }
            } else {
                $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'uploading';
            }
        }

        if ($autouploader['snapshots'][$autouploader['current_snapshot']]['status'] == 'uploading') {
            if (count($autouploader['snapshots'][$autouploader['current_snapshot']]['steps']) == 0) {
                $this->log('info', 'Autouploader: Preparing cloud upload');
                $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'uploading';
                $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Preparing snapshot for upload';
                $autouploader['started'] = time();
                $this->update_options('autouploader', $autouploader);
                $res = $wp_reset_cloud->cloud_action(array('action' => 'snapshot_upload', 'parameters' => $autouploader['current_snapshot']));
                if (is_wp_error($res)) {
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'error';
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = $res->get_error_message();
                    $this->log('error', 'Error preparing snapshot for upload ' . $res->get_error_message());
                } else {
                    if ($res['continue'] == 1) {
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0] = $res;
                    }
                    $this->log('info', 'Autouploader: Cloud response is ' . serialize($res));
                    if ($res['action'] == 'snapshots_refresh') {
                        $wp_reset_cloud->cloud_action(array('action' => 'snapshots_refresh'));
                    }
                }
                $autouploader['started'] = false;
            } else {
                $this->log('info', 'Autouploader: Running cloud step ' . serialize($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]));
                $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'uploading';
                $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Uploading ... ';
                switch ($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['action']) {
                    case 'snapshots_refresh':
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Refreshing cloud snapshots';
                        break;
                    case 'upload_part':
                        if (!empty($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['message'])) {
                            $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['message'];
                        }
                        break;
                    case 'snapshot_delete_local':
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Deleting local copy of snapshot';
                        break;
                }
                $autouploader['started'] = time();
                $this->update_options('autouploader', $autouploader);
                $res = $wp_reset_cloud->cloud_action($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]);
                $do_refresh = false;
                if ($autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0]['action'] == 'snapshot_delete_local') {
                    $do_refresh = true;
                }
                if (is_wp_error($res)) {
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'error';
                    $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = $res->get_error_message();
                    $this->log('error', 'Cloud error: ' . serialize($res));
                } else {
                    if (is_array($res) && array_key_exists('continue', $res) && $res['continue'] == 1 && $res['action'] != 'snapshots_refresh') {
                        $this->log('info', 'Cloud next step: ' . serialize($res));
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['steps'][0] = $res;
                    } else {
                        $wp_reset_cloud->cloud_action(array('action' => 'snapshots_refresh'));
                        $this->log('info', 'Cloud: Snapshot uploaded');
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['status'] = 'finished';
                        $autouploader['snapshots'][$autouploader['current_snapshot']]['message'] = 'Snapshot ' . $autouploader['current_snapshot'] . ' uploaded sucessfully!';
                    }
                }
                if ($do_refresh) {
                    $this->log('info', 'Autouploader: Refreshing snapshots');
                    $wp_reset_cloud->cloud_action(array('action' => 'snapshots_refresh'));
                }
                $autouploader['started'] = false;
            }
        }

        if ($autouploader['snapshots'][$autouploader['current_snapshot']]['status'] == 'finished') {
            if($options['snapshots_upload_delete'] != true && array_key_exists($autouploader['current_snapshot'], $snapshots)) {
                unset($snapshots[$autouploader['current_snapshot']]['autoupload']);
                update_option('wp-reset-snapshots', $snapshots);
            }

            $res = $wp_reset_cloud->cloud_action(array('action' => 'snapshots_refresh'));
            if (is_wp_error($res)) {
                $this->log('error', 'Cloud: Error refreshing snapshots');
            } else {
                $this->log('info', 'Cloud: Snapshots Refreshed!');
            }
            unset($autouploader['snapshots'][$autouploader['current_snapshot']]);
            $autouploader['current_snapshot'] = false;
        }

        $this->update_options('autouploader', $autouploader);
        return array('uid' => $autouploader['current_snapshot'], 'status' => $autouploader['status'], 'message' => $autouploader['message']);
    }

    /**
     * Get full path, file name or URL of recovery script installed in wp-content
     *
     * @return string
     */
    function wpr_recovery_path($url = false, $full = false)
    {
        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);
        $search = glob($wp_content_dir . 'wpr_recovery_*');

        if (empty($search)) {
            return false;
        }

        if ($full) {
            return $search[0];
        }

        if ($url) {
            return content_url(basename($search[0]));
        }

        return basename($search[0]);
    } // wpr_recovery_path


    /**
     * Intall recovery script
     *
     * @return bool|wp_error
     */
    function do_install_recovery()
    {
        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $recovery_filename = $this->wpr_recovery_path();
        if (!$recovery_filename) {
            $recovery_filename = 'wpr_recovery_' . substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 32)), 0, 32) . '.php';
        }

        $recovery_files = new ZipArchive;
        $recovery_files->open(dirname(WP_RESET_FILE) . '/misc/wpr_recovery.zip');

        if ($recovery_file_contents = $recovery_files->getFromName('wpr_recovery.php')) {
            file_put_contents($wp_content_dir . $recovery_filename, $recovery_file_contents);
        } else {
            return new WP_Error('1', 'An error occurred extracting the recovery script.');
        }

        if (!file_exists($wp_content_dir . $recovery_filename)) {
            return new WP_Error('1', 'An error occurred writing the recovery script to wp-content.');
        }

        $password = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 8)), 0, 8);
        $this->update_recovery_password($password);

        return array('url' => content_url($recovery_filename), 'pass' => $password);
    } // do_install_recovery


    /**
     * Update recovery script
     *
     * @return bool|wp_error
     */
    function do_update_recovery()
    {
        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $recovery_filename = $this->wpr_recovery_path();
        if (!$recovery_filename) {
            $recovery_filename = 'wpr_recovery_' . substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 32)), 0, 32) . '.php';
            $recovery_password = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 8)), 0, 8);
        } else {
            $recovery_password = $this->get_recovery_password();
        }

        $recovery_files = new ZipArchive;
        $recovery_files->open(dirname(WP_RESET_FILE) . '/misc/wpr_recovery.zip');

        if ($recovery_file_contents = $recovery_files->getFromName('wpr_recovery.php')) {
            file_put_contents($wp_content_dir . $recovery_filename, $recovery_file_contents);
        } else {
            return new WP_Error('1', 'An error occurred extracting the recovery script.');
        }

        if (!file_exists($wp_content_dir . $recovery_filename)) {
            return new WP_Error('1', 'An error occurred writing the recovery script to wp-content.');
        }

        $this->update_recovery_password($recovery_password);

        return array('url' => content_url($recovery_filename), 'pass' => $recovery_password);
    } // do_install_recovery


    /**
     * Uninstall recovery script
     *
     * @return bool|wp_error
     */
    function do_uninstall_recovery()
    {
        $recovery_filename = $this->wpr_recovery_path(false, true);

        unlink($recovery_filename);

        if (file_exists($recovery_filename)) {
            return new WP_Error('1', 'Could not delete ' . $recovery_filename);
        }

        return true;
    } // do_uninstall_recovery

    /**
     * Intall/update recovery script
     *
     * @return bool|wp_error
     */
    function send_recovery_script_email($admin_email)
    {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $body = '<b>Thank you for installing WP Reset!<br>';

        $body = 'WP Reset also provides you with a standalone Emergency recovery script that you can upload to your website to fix your WordPress installation if it is no longer accessible, such as in a White Screen of Death (WSOD) situation<br />';
        $body .= 'The Emergency recovery script provides you with the following tools:<br />';
        $body .= '<ul>';
        $body .= '<li>View server information</li>';
        $body .= '<li>Scan WordPress core files for changes</li>';
        $body .= '<li>Reinstall WordPress core files</li>';
        $body .= '<li>Activate/Deactivate plugins</li>';
        $body .= '<li>Deactivate themes</li>';
        $body .= '<li>Reset user database prefix</li>';
        $body .= '<li>Create Administator accounts</li>';
        $body .= '<li>Update WordPress Address and Site Address</li>';
        $body .= '</ul>';

        $body .= '<br /><br />';

        $recovery_url = $this->wpr_recovery_path(true);
        if ($recovery_url !== false) {
            $body .= 'The Emergency recovery script has been installed on your website and you can access it here: <br />';
            $body .= '<a href="' . $recovery_url . '" target="_blank">' . $recovery_url . '</a>';
            $recovery_password = $this->get_recovery_password();
            $body .= '<br />The password for the Emergency recovery script is ' . (!empty($recovery_password) ? '<strong style="color:#F00">' . $recovery_password . '</strong>' : '<strong style="color:#F00">not set</strong>');
            $body .= '<br /><br />';
        }

        $body .= 'To install the WP Reset Emergency recovery script follow these instructions:';
        $body .= '<ul>';
        $body .= '<li><a href="' . trailingslashit($this->licensing_servers[0]) . 'wpr_recovery_download.php">Download the WP Reset Emergency recovery script</a></li>';
        $body .= '<li>In order to prevent unauthorized acccess, the downloaded script already has a random, unique file name but if you wish you can rename it</li>';
        $body .= '<li>Upload the recovery script to your website via FTP or though your Hostin Provider\'s Control Panel</li>';
        $body .= '<li>Open the recovery script and perform the recovery actions you need</li>';
        $body .= '</ul>';

        if (!wp_mail($admin_email, 'WP Reset Emergency recovery', $body, $headers)) {
            return new WP_Error('1', 'An error occurred sending the email with the Emergency recovery script installation instructions.');
        }

        return true;
    }


    function get_recovery_password()
    {
        $constant = 'WPR_RECOVERY_PASS';

        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $recovery_filename = $wp_content_dir . $this->wpr_recovery_path();

        if (!$recovery_filename) {
            return new WP_Error('1', 'Emergency recovery script not installed.');
        }

        $file_contents = file_get_contents($recovery_filename);

        preg_match_all('/define\(\s*[\'|"]\s*(' . $constant . ')\s*[\'|"]\s*,\s*(false|true|[\'|"].*[\'|"])\s*\);/i', $file_contents, $matches);

        if (count($matches[2]) > 0) {
            return trim(trim($matches[2][0], '\''), '"');
        } else {
            return false;
        }
    }


    function get_recovery_version()
    {
        $constant = 'WPR_RECOVERY_VER';

        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $recovery_filename = $wp_content_dir . $this->wpr_recovery_path();

        if (!$recovery_filename) {
            return new WP_Error('1', 'Emergency recovery script not installed.');
        }

        $file_contents = file_get_contents($recovery_filename);

        preg_match_all('/define\(\s*[\'|"]\s*(' . $constant . ')\s*[\'|"]\s*,\s*(false|true|[\'|"].*[\'|"])\s*\);/i', $file_contents, $matches);

        if (count($matches[2]) > 0) {
            return trim(trim($matches[2][0], '\''), '"');
        } else {
            return '1.0';
        }
    }


    function update_recovery_password($new_value)
    {
        $constant = 'WPR_RECOVERY_PASS';

        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $recovery_filename = $wp_content_dir . $this->wpr_recovery_path();

        if (!$recovery_filename) {
            return new WP_Error('1', 'Emergency recovery script not installed.');
        }

        $file_contents = file_get_contents($recovery_filename);

        // if define already exists update it
        if (preg_match_all('/define\([\'|"]\s*(' . $constant . ')\s*[\'|"]\s*,\s*(false|true|[\'|"].*[\'|"])\s*\);/i', $file_contents, $matches)) {
            if (is_bool($new_value)) {
                if ($new_value) {
                    $file_contents = str_replace($matches[0], "define('" . $constant . "', true);", $file_contents);
                } else {
                    $file_contents = str_replace($matches[0], "define('" . $constant . "', false);", $file_contents);
                }
            } else {
                $file_contents = str_replace($matches[0], "define('" . $constant . "', '" . $new_value . "');", $file_contents);
            }
            file_put_contents($recovery_filename, $file_contents, LOCK_EX);
        } else {
            // if define does not exists insert it in a new line before require_once(ABSPATH.'wp-settings.php');
            if (is_bool($new_value)) {
                if ($new_value) {
                    $new_define_line_contents = 'define(\'' . $constant . '\', true);';
                } else {
                    $new_define_line_contents = 'define(\'' . $constant . '\', false);';
                }
            } else {
                $new_define_line_contents = 'define(\'' . $constant . '\', \'' . $new_value . '\');';
            }

            $config_file = file($recovery_filename);
            foreach ($config_file as $line_num => $line) {
                if (strpos(str_replace(' ', '', str_replace('"', '\'', $line)), 'require_once(ABSPATH.\'wp-settings.php\');') !== false) {
                    $wp_settings_require_line = $line_num;
                    break;
                }
            }
            array_splice($config_file, $wp_settings_require_line, 0, $new_define_line_contents . PHP_EOL);
            file_put_contents($recovery_filename, implode('', $config_file), LOCK_EX);
        }
    } // update_define

    /**
     * Reinstall / reset the WP site
     * There are no failsafes in the function - it reinstalls when called
     * Redirects when done if not CLI
     *
     * @param array  $params  Optional.
     *
     * @return null
     */
    function do_reinstall($params = array())
    {
        global $current_user, $wpdb;

        // only admins can reset; double-check
        if (!$this->is_cli_running() && !current_user_can('administrator')) {
            return new WP_Error('1', 'Only administrators can reset.');
        }

        $params = shortcode_atts(array('reactivate_webhooks' => false, 'reactivate_theme' => false, 'reactivate_wpreset' => false, 'reactivate_plugins' => false), (array) $params);

        // make sure the function is available to us
        if (!function_exists('wp_install')) {
            require ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $GLOBALS['wpr_autosnapshot_done'] = true;
        do_action('wp_reset_before_reset_site', $params);

        // save values that need to be restored after reset
        $blogname = get_option('blogname');
        $blog_public = get_option('blog_public');
        $wplang = get_option('WPLANG');
        $siteurl = get_option('siteurl');
        $home = get_option('home');
        $snapshots = $this->get_snapshots();
        $gmt_offset = get_option('gmt_offset');
        $timezone_string = get_option('timezone_string');
        $wf_licensing_wpr = get_option('wf_licensing_wpr');

        $active_plugins = get_option('active_plugins');
        $active_theme = wp_get_theme();

        if (!empty($params['reactivate_webhooks'])) {
            $wpwh1 = get_option('wpwhpro_active_webhooks');
            $wpwh2 = get_option('wpwhpro_activate_translations');
            $wpwh3 = get_option('ironikus_webhook_webhooks');
        }

        // for WP-CLI
        if (!$current_user->ID) {
            $tmp = get_users(array('role' => 'administrator', 'order' => 'ASC', 'order_by' => 'ID'));
            if (empty($tmp[0]->user_login)) {
                return new WP_Error(1, 'Reset failed. Unable to find any admin users in database.');
            }
            $current_user = $tmp[0];
        }

        // delete custom tables with WP's prefix
        $prefix = str_replace('_', '\_', $wpdb->prefix);
        $tables = $wpdb->get_col("SHOW TABLES LIKE '{$prefix}%'");
        foreach ($tables as $table) {
            $wpdb->query('SET foreign_key_checks = 0');
            $wpdb->query("DROP TABLE `$table`");
        }

        // supress errors for WP_CLI
        $result = @wp_install($blogname, $current_user->user_login, $current_user->user_email, $blog_public, '', md5(rand()), $wplang);
        $user_id = $result['user_id'];

        // restore user pass
        $query = $wpdb->prepare("UPDATE {$wpdb->users} SET user_pass = %s, user_activation_key = '' WHERE ID = %d LIMIT 1", array($current_user->user_pass, $user_id));
        $wpdb->query($query);

        // restore rest of the settings including WP Reset's
        update_option('siteurl', $siteurl);
        update_option('home', $home);
        update_option('wp-reset', $this->options);
        update_option('wp-reset-snapshots', $snapshots);
        update_option('gmt_offset', $gmt_offset);
        update_option('timezone_string', $timezone_string);
        update_option('wf_licensing_wpr', $wf_licensing_wpr);

        update_option($wpdb->prefix . 'user_roles', array(
            'administrator' =>
            array(
                'name' => 'Administrator',
                'capabilities' =>
                array(
                    'switch_themes' => true,
                    'edit_themes' => true,
                    'activate_plugins' => true,
                    'edit_plugins' => true,
                    'edit_users' => true,
                    'edit_files' => true,
                    'manage_options' => true,
                    'moderate_comments' => true,
                    'manage_categories' => true,
                    'manage_links' => true,
                    'upload_files' => true,
                    'import' => true,
                    'unfiltered_html' => true,
                    'edit_posts' => true,
                    'edit_others_posts' => true,
                    'edit_published_posts' => true,
                    'publish_posts' => true,
                    'edit_pages' => true,
                    'read' => true,
                    'level_10' => true,
                    'level_9' => true,
                    'level_8' => true,
                    'level_7' => true,
                    'level_6' => true,
                    'level_5' => true,
                    'level_4' => true,
                    'level_3' => true,
                    'level_2' => true,
                    'level_1' => true,
                    'level_0' => true,
                    'edit_others_pages' => true,
                    'edit_published_pages' => true,
                    'publish_pages' => true,
                    'delete_pages' => true,
                    'delete_others_pages' => true,
                    'delete_published_pages' => true,
                    'delete_posts' => true,
                    'delete_others_posts' => true,
                    'delete_published_posts' => true,
                    'delete_private_posts' => true,
                    'edit_private_posts' => true,
                    'read_private_posts' => true,
                    'delete_private_pages' => true,
                    'edit_private_pages' => true,
                    'read_private_pages' => true,
                    'delete_users' => true,
                    'create_users' => true,
                    'unfiltered_upload' => true,
                    'edit_dashboard' => true,
                    'update_plugins' => true,
                    'delete_plugins' => true,
                    'install_plugins' => true,
                    'update_themes' => true,
                    'install_themes' => true,
                    'update_core' => true,
                    'list_users' => true,
                    'remove_users' => true,
                    'promote_users' => true,
                    'edit_theme_options' => true,
                    'delete_themes' => true,
                    'export' => true,
                ),
            ),
            'editor' =>
            array(
                'name' => 'Editor',
                'capabilities' =>
                array(
                    'moderate_comments' => true,
                    'manage_categories' => true,
                    'manage_links' => true,
                    'upload_files' => true,
                    'unfiltered_html' => true,
                    'edit_posts' => true,
                    'edit_others_posts' => true,
                    'edit_published_posts' => true,
                    'publish_posts' => true,
                    'edit_pages' => true,
                    'read' => true,
                    'level_7' => true,
                    'level_6' => true,
                    'level_5' => true,
                    'level_4' => true,
                    'level_3' => true,
                    'level_2' => true,
                    'level_1' => true,
                    'level_0' => true,
                    'edit_others_pages' => true,
                    'edit_published_pages' => true,
                    'publish_pages' => true,
                    'delete_pages' => true,
                    'delete_others_pages' => true,
                    'delete_published_pages' => true,
                    'delete_posts' => true,
                    'delete_others_posts' => true,
                    'delete_published_posts' => true,
                    'delete_private_posts' => true,
                    'edit_private_posts' => true,
                    'read_private_posts' => true,
                    'delete_private_pages' => true,
                    'edit_private_pages' => true,
                    'read_private_pages' => true,
                ),
            ),
            'author' =>
            array(
                'name' => 'Author',
                'capabilities' =>
                array(
                    'upload_files' => true,
                    'edit_posts' => true,
                    'edit_published_posts' => true,
                    'publish_posts' => true,
                    'read' => true,
                    'level_2' => true,
                    'level_1' => true,
                    'level_0' => true,
                    'delete_posts' => true,
                    'delete_published_posts' => true,
                ),
            ),
            'contributor' =>
            array(
                'name' => 'Contributor',
                'capabilities' =>
                array(
                    'edit_posts' => true,
                    'read' => true,
                    'level_1' => true,
                    'level_0' => true,
                    'delete_posts' => true,
                ),
            ),
            'subscriber' =>
            array(
                'name' => 'Subscriber',
                'capabilities' =>
                array(
                    'read' => true,
                    'level_0' => true,
                ),
            ),
        ));

        // remove password nag
        if (get_user_meta($user_id, 'default_password_nag')) {
            update_user_meta($user_id, 'default_password_nag', false);
        }
        if (get_user_meta($user_id, $wpdb->prefix . 'default_password_nag')) {
            update_user_meta($user_id, $wpdb->prefix . 'default_password_nag', false);
        }

        $meta = $this->get_meta();
        $meta['reset_count']++;
        $this->update_options('meta', $meta);

        // reactivate theme
        if (!empty($params['reactivate_theme'])) {
            switch_theme($active_theme->get_stylesheet());
        }

        // reactivate WP Reset
        if (!empty($params['reactivate_wpreset'])) {
            activate_plugin(plugin_basename(__FILE__));
        }

        // reactivate WP Webhooks
        if (!empty($params['reactivate_webhooks'])) {
            activate_plugin('wp-webhooks/wp-webhooks.php');
            activate_plugin('wpwh-wp-reset-webhook-integration/wpwhpro-wp-reset-webhook-integration.php');

            update_option('wpwhpro_active_webhooks', $wpwh1);
            update_option('wpwhpro_activate_translations', $wpwh2);
            update_option('ironikus_webhook_webhooks', $wpwh3);
        }

        // reactivate all plugins
        if (!empty($params['reactivate_plugins'])) {
            foreach ($active_plugins as $plugin_file) {
                activate_plugin($plugin_file);
            }
        }

        do_action('wp_reset_reset_site', $params);

        if (!$this->is_cli_running()) {
            wp_clear_auth_cookie();
            wp_set_auth_cookie($user_id);

            return admin_url() . '?wp-reset=success';
        }
    } // do_reinstall


    /**
     * Add "Open WP Reset Tools" action link to plugins table, left part
     *
     * @param array  $links  Initial list of links.
     *
     * @return array
     */
    function plugin_action_links($links)
    {
        $settings_link = '<a href="' . admin_url('tools.php?page=wp-reset') . '" title="' . __('Open WP Reset PRO Tools', 'wp-reset') . '">' . __('Open Reset Tools', 'wp-reset') . '</a>';

        array_unshift($links, $settings_link);

        return $links;
    } // plugin_action_links


    /**
     * Add links to plugin's description in plugins table
     *
     * @param array  $links  Initial list of links.
     * @param string $file   Basename of current plugin.
     *
     * @return array
     */
    function plugin_meta_links($links, $file)
    {
        if ($file !== plugin_basename(__FILE__)) {
            return $links;
        }

        if (!WP_Reset_Utility::whitelabel_filter()) {
            unset($links[1]);
            unset($links[2]);
            return $links;
        }

        $support_link = '<a target="_blank" href="' . $this->generate_web_link('plugins-table-right', '/support/') . '" title="' . __('Get help', 'wp-reset') . '">' . __('Support', 'wp-reset') . '</a>';
        $home_link = '<a target="_blank" href="' . $this->generate_web_link('plugins-table-right') . '" title="' . __('Plugin Homepage', 'wp-reset') . '">' . __('Plugin Homepage', 'wp-reset') . '</a>';
        $rate_link = '<a target="_blank" href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post" title="' . __('Rate the plugin', 'wp-reset') . '">' . __('Rate the plugin ★★★★★', 'wp-reset') . '</a>';

        $links[] = $support_link;
        $links[] = $home_link;
        $links[] = $rate_link;

        return $links;
    } // plugin_meta_links


    /**
     * Test if we're on WPR's admin page
     *
     * @return bool
     */
    function is_plugin_page()
    {
        $current_screen = get_current_screen();

        if (!empty($current_screen) && $current_screen->id == 'tools_page_wp-reset') {
            return true;
        } else {
            return false;
        }
    } // is_plugin_page


    /**
     * Add powered by text in admin footer
     *
     * @param string  $text  Default footer text.
     *
     * @return string
     */
    function admin_footer_text($text)
    {
        if (!$this->is_plugin_page() || !WP_Reset_Utility::whitelabel_filter()) {
            return $text;
        }

        $text = '<i class="wpr-footer"><a href="' . $this->generate_web_link('admin_footer') . '" title="' . __('Visit WP Reset page for more info', 'wp-reset') . '" target="_blank">WP Reset PRO</a> v' . $this->version . '. Please <a target="_blank" href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post" title="Rate the plugin">rate the plugin <span>★★★★★</span></a> to help us spread the word. Thank you from the WP Reset team!</i>';

        return $text;
    } // admin_footer_text


    /**
     * Loads plugin's translated strings
     *
     * @return null
     */
    function load_textdomain()
    {
        load_plugin_textdomain('wp-reset');
    } // load_textdomain


    /**
     * Inform the user that WordPress has been successfully reset
     *
     * @return null
     */
    function notice_successful_reset()
    {
        global $current_user;

        // only admins can see notifications
        if (!current_user_can('administrator')) {
            return;
        }

        if (!empty($_GET['wp-reset']) && $_GET['wp-reset'] == 'success') {
            echo '<div id="message" class="updated"><p>' . sprintf(__('<b>Site has been reset</b> to default settings. User "%s" was restored with the password unchanged. Open <a href="%s">WP Reset PRO</a> to do another reset.', 'wp-reset'), $current_user->user_login, admin_url('tools.php?page=wp-reset')) . '</p></div>';
        }
    } // notice_successful_reset


    /**
     * Generate a button that initiates backup creation and download
     *
     * @param string  $description  Snapshot description; if set user won't be asked for one
     *
     * @return string
     */
    function get_create_snapshot_button($description = '')
    {
        $out = '';
        $out .= '<a data-snapshot-description="' . $description . '" class="button tools-create-snapshot" href="#">Create snapshot</a>';

        return $out;
    } // get_create_snapshot_button


    /**
     * Generate card header including title and action buttons
     *
     * @param string  $title  Card title.
     * @param string  $card_id  Card element #ID.
     * @param array  $params  Individual icons arguments
     *
     * @return string
     */
    function get_card_header($title, $card_id, $params = array())
    {
        $params = shortcode_atts(array('documentation_link' => false, 'iot_button' => false, 'collapse_button' => false, 'create_snapshot' => false), (array) $params);

        if (false == WP_Reset_Utility::whitelabel_filter()) {
            $params['documentation_link'] = false;
        }

        $out = '';
        $out .= '<h4 id="' . $card_id . '"><span class="card-name">' . htmlspecialchars($title) . '</span>';
        $out .= '<div class="card-header-right">';
        if ($params['documentation_link']) {
            $out .= '<a data-tool-title="' . esc_attr($title) . '" class="documentation-link" href="#" title="' . __('Open documentation for this tool', 'wp-reset') . '" data-tooltip="' . __('Open documentation for this tool', 'wp-reset') . '"><span class="dashicons dashicons-editor-help"></span></a>';
        }
        if ($params['iot_button']) {
            $out .= '<a class="scrollto" href="#iot" title="Jump to Index of Tools" data-tooltip="Jump to Index of Tools"><span class="dashicons dashicons-screenoptions"></span></a>';
        }
        if ($params['create_snapshot']) {
            $out .= '<a id="create-new-snapshot-primary" title="Create a new snapshot" href="#" class="button button-primary create-new-snapshot">' . __('Create Snapshot', 'wp-reset') . '</a>';
        }
        if ($params['collapse_button']) {
            $out .= '<a class="toggle-card" href="#" title="' . __('Collapse / expand box', 'wp-reset') . '" data-tooltip="' . __('Collapse / expand box', 'wp-reset') . '"><span class="dashicons dashicons-arrow-up-alt2"></span></a>';
        }
        $out .= '</div></h4>';

        return $out;
    } // get_card_header


    /**
     * Generates the complete TR for a single snapshot
     *
     * @param array $ss Snapshot array with all params
     * @param string $ss_type user or auto
     * @return string
     */
    function get_snapshot_row($ss, $ss_type = 'user')
    {
        $options = $this->get_options();

        $out = '';

        if (!empty($ss['name'])) {
            $name = $ss['name'];
        } else {
            $name = 'created on ' . date(get_option('date_format'), strtotime($ss['timestamp'])) . ' @ ' . date(get_option('time_format'), strtotime($ss['timestamp']));
        }

        $out .= '<tr ';

        if (isset($ss['partial']) && $ss['partial'] == true) {
            $out .= 'class="snapshot-partial"';
        }

        $out .= ' id="wpr-ss-' . $ss['uid'] . '" data-ss-uid="' . $ss['uid'] . '" data-ss-type="' . $ss_type . '">';
        $out .= '<td class="ss-checkbox"><input type="checkbox" name="' . ($ss['auto'] == true? 'selected_autosnapshots':'selected_snapshots') . '" value="' . $ss['uid'] . '" /> </td>';
        $out .= '<td class="ss-date">';
        if (current_time('timestamp') - strtotime($ss['timestamp']) > 12 * HOUR_IN_SECONDS) {
            $out .= date(get_option('date_format'), strtotime($ss['timestamp'])) . '<br>@ ' . date(get_option('time_format'), strtotime($ss['timestamp']));
        } else {
            $out .= human_time_diff(strtotime($ss['timestamp']), current_time('timestamp')) . ' ago';
        }
        $out .= '</td>';

        $out .= '<td class="ss-name">';
        if (!empty($ss['name'])) {
            $out .= '<b class="snapshot-name">' . htmlspecialchars(html_entity_decode($ss['name'])) . '</b>';
        } else {
            $out .= '<b class="snapshot-name"><i>Click to add description.</i></b>';
        }
        $out .= '<br>';
        $out .= 'Contains ' . $ss['tbl_core'] . ' core ';
        if ($ss['tbl_custom']) {
            $out .= '&amp; ' . $ss['tbl_custom'] . ' custom table' . ($ss['tbl_custom'] == 1 ? '' : 's');
        } else {
            $out .= 'tables';
        }
        $out .= ' totaling ' . number_format($ss['tbl_rows']) . ' rows';
        if (!empty($ss['plugins'])) {
            $out .= ' as well as files for ' . sizeof($ss['plugins']) . ' plugin' . (sizeof($ss['plugins']) == 1 ? '' : 's') . ': ' . implode(', ', array_keys($ss['plugins']));;
        }
        if (!empty($ss['themes'])) {
            $out .= ' as well as files for ' . sizeof($ss['themes']) . ' theme' . (sizeof($ss['themes']) == 1 ? '' : 's') . ': ' . implode(', ', array_keys($ss['themes']));;
        }

        $out .= '<br />';

        if (isset($ss['local']) && $ss['local'] == true) {
            $out .= '<span title="Available locally" data-tooltip="Available locally" class="dashicons"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="hdd" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-hdd fa-w-18 fa-3x"><path fill="currentColor" d="M567.403 235.642L462.323 84.589A48 48 0 0 0 422.919 64H153.081a48 48 0 0 0-39.404 20.589L8.597 235.642A48.001 48.001 0 0 0 0 263.054V400c0 26.51 21.49 48 48 48h480c26.51 0 48-21.49 48-48V263.054c0-9.801-3-19.366-8.597-27.412zM153.081 112h269.838l77.913 112H75.168l77.913-112zM528 400H48V272h480v128zm-32-64c0 17.673-14.327 32-32 32s-32-14.327-32-32 14.327-32 32-32 32 14.327 32 32zm-96 0c0 17.673-14.327 32-32 32s-32-14.327-32-32 14.327-32 32-32 32 14.327 32 32z" class=""></path></svg></span>';
        }
        if (isset($ss['cloud']) && $ss['cloud'] == true) {
            $out .= '<span title="Available in the cloud" data-tooltip="Available in the cloud" class="dashicons dashicons-cloud"></span>';
        }

        $out .= '</td>';

        $out .= '<td class="ss-size">';
        $tmp = $ss['tbl_size'];
        if (!empty($ss['file_size'])) {
            $tmp += $ss['file_size'];
        }
        $out .= WP_Reset_Utility::format_size($tmp);
        $out .= '</td>';

        $out .= '<td class="ss-actions">';
        $out .= '<div class="dropdown">
        <a class="button dropdown-toggle" href="#">Actions</a>
        <div class="dropdown-menu">';
        $menu_item = false;
        if (!(isset($ss['partial']) && $ss['partial'] == true)) {
            if (isset($ss['local']) && $ss['local'] == true) {
                $menu_item = true;
                $out .= '<a data-btn-confirm="Restore snapshot" data-text-wait="Restoring snapshot. Please wait." data-text-confirm="Are you sure you want to restore the selected snapshot? There is NO UNDO.<br>Restoring the snapshot will delete all current standard and custom tables and replace them with tables from the snapshot." data-text-done="Snapshot has been restored. Click OK to reload the page with new data." title="Restore snapshot by overwriting current database tables" href="#" class="ss-action restore-snapshot dropdown-item" data-ss-uid="' . $ss['uid'] . '">Restore snapshot</a>';
                $out .= '<a data-title="Current DB tables compared to snapshot %s" data-wait-msg="Comparing. Please wait." data-name="' . $name . '" title="Compare snapshot to current database tables" href="#" class="ss-action compare-snapshot dropdown-item" data-ss-uid="' . $ss['uid'] . '">Compare snapshot to current data</a>';
                $out .= '<a data-success-msg="Snapshot export created!<br><a href=\'%s\'>Download it</a>" data-wait-msg="Exporting snapshot. Please wait." title="Download snapshot as gzipped SQL dump" href="#" class="ss-action download-snapshot dropdown-item" data-ss-uid="' . $ss['uid'] . '">Download snapshot</a>';
                $out .= '<a title="Edit snapshot description" href="#" class="ss-action edit-snapshot-description dropdown-item">Edit snapshot description</a>';
            }
        }

        if (isset($ss['local']) && $ss['local'] == true) {
            $menu_item = true;
            $out .= '<a data-btn-confirm="Delete snapshot" data-text-wait="Deleting snapshot. Please wait." data-text-confirm="Are you sure you want to delete the selected snapshot? There is NO UNDO.<br><br>Deleting the snapshot will not affect the active database tables in any way.<br><br>If the snapshot is uploaded to the cloud it will ONLY be deleted locally. Cloud copy will not be touched." data-text-done="Snapshot has been deleted" title="Permanently delete snapshot" href="#" class="ss-action delete-snapshot delete-button dropdown-item" data-ss-uid="' . $ss['uid'] . '">Delete snapshot</a>';
        }


        if (!empty($options['cloud_service']) && array_key_exists($options['cloud_service'], $this->cloud_services) && !(isset($ss['partial']) && $ss['partial'] == true)) {
            if (isset($ss['local']) && $ss['local'] == true && !(isset($ss['cloud']) && $ss['cloud'] == true)) {
                $menu_item = true;
                $out .= '<a data-text-wait="Uploading snapshot to ' . $this->cloud_services[$options['cloud_service']] . '. Please wait." data-text-done-singular="Snapshot has been uploaded." title="Upload Snapshot to ' . $this->cloud_services[$options['cloud_service']] . '" href="#" class="ss-action cloud-upload-snapshot dropdown-item" data-ss-uid="' . $ss['uid'] . '"><span title="Cloud Actions" class="dashicons dashicons-cloud cloud-action-icon"></span> Upload Snapshot to ' . $this->cloud_services[$options['cloud_service']] . '</a>';
            }

            if (isset($ss['cloud']) && $ss['cloud'] == true) {
                $menu_item = true;
                $out .= '<a data-text-wait="Downloading snapshot from ' . $this->cloud_services[$options['cloud_service']] . '. Please wait." data-text-done-singular="Snapshot has been downloaded from ' . $this->cloud_services[$options['cloud_service']] . '" title="Download snapshot from ' . $this->cloud_services[$options['cloud_service']] . '" href="#" class="ss-action cloud-download-snapshot dropdown-item" data-ss-uid="' . $ss['uid'] . '"><span title="Cloud Actions" class="dashicons dashicons-cloud cloud-action-icon"></span> Download Snapshot from ' . $this->cloud_services[$options['cloud_service']] . '</a>';
                $out .= '<a data-btn-confirm="Delete snapshot from ' . $this->cloud_services[$options['cloud_service']] . '" data-text-wait="Deleting snapshot from ' . $this->cloud_services[$options['cloud_service']] . '. Please wait." data-text-confirm="Are you sure you want to delete the selected snapshot from ' . $this->cloud_services[$options['cloud_service']] . '? There is NO UNDO." data-text-done-singular="Snapshot has been deleted from ' . $this->cloud_services[$options['cloud_service']] . '." title="Delete Snapshot from ' . $this->cloud_services[$options['cloud_service']] . '" href="#" class="ss-action cloud-delete-snapshot delete-button dropdown-item" data-ss-uid="' . $ss['uid'] . '"><span title="Cloud Actions" class="dashicons dashicons-cloud cloud-action-icon"></span> Delete Snapshot from ' . $this->cloud_services[$options['cloud_service']] . '</a>';
            }
        }
        if($menu_item == false){
            $out .= '<a href="#tab-settings" class="dropdown-item">No cloud service enabled</a>';
        }
        $out .= '</div></div>';
        $out .= '</td>';
        $out .= '</tr>';

        return $out;
    } // get_snapshot_row


    /**
     * Generate tool icons and description detailing what it modifies or doesn't
     *
     * @param bool  $modify_files  Does the tool modify files?
     * @param bool  $modify_db  Does the tool modify the database?
     * @param bool  $plural  Is there more than one tool in the set?
     *
     * @return string
     */
    function get_tool_icons($modify_files = false, $modify_db = false, $plural = false)
    {
        $out = '';

        $out .= '<p class="tool-icons">';
        $out .= '<i class="icon-doc-text-inv' . ($modify_files ? ' red' : '') . '"></i> ';
        $out .= '<i class="icon-database' . ($modify_db ? ' red' : '') . '"></i> ';

        if ($plural) {
            if ($modify_files && $modify_db) {
                $out .= 'these tools <b>modify files &amp; the database</b>.';
            } elseif (!$modify_files && $modify_db) {
                $out .= 'these tools <b>modify the database</b> but they don\'t modify any files.';
            } elseif ($modify_files && !$modify_db) {
                $out .= 'these tools <b>modify files</b> but they don\'t modify the database.';
            } else {
                $out .= 'these tools don\'t modify any files or the database.';
            }
        } else {
            if ($modify_files && $modify_db) {
                $out .= 'this tool <b>modifies files &amp; the database</b>.';
            } elseif (!$modify_files && $modify_db) {
                $out .= 'this tool <b>modifies the database</b> but it doesn\'t modify any files.';
            } elseif ($modify_files && !$modify_db) {
                $out .= 'this tool <b>modifies files</b> but it doesn\'t modify the database.';
            } else {
                $out .= 'this tool doesn\'t modify any files or the database.';
            }
        }
        $out .= '</p>';

        return $out;
    } // get_tool_icons


    /**
     * Main/starter function for displaying plugin's admin page
     *
     * @return null
     */
    function plugin_page()
    {
        // double check for admin privileges
        if (!current_user_can('administrator')) {
            wp_die(__('Sorry, you are not allowed to access this page.', 'wp-reset'));
        }

        global $wp_reset_collections, $wp_reset_licensing;
        $options = $this->get_options();

        echo '<div class="wrap">';

        echo '<header>';
        echo '<div class="wpr-container">';
        if ($options['debug'] == true) {
            echo '<span style="font-size: 48px; line-height: 30px; width: 50px; color: #dd3036;" class="dashicons dashicons-admin-tools"></span>';
        }
        echo '<img id="logo-icon" src="' . $this->plugin_url . 'img/wp-reset-logo.png" title="' . __('WP Reset PRO', 'wp-reset') . '" alt="' . __('WP Reset PRO', 'wp-reset') . '">';
        echo '</div>';
        echo '</header>';

        echo '<div id="loading-tabs"><img class="wpr_rotating" src="' . $this->plugin_url . 'img/wp-reset-icon.png' . '" alt="Loading. Please wait." title="Loading. Please wait."></div>';
        echo '<div id="wp-reset-tabs" class="ui-tabs" style="display: none;">';

        echo '<nav>';
        echo '<div class="wpr-container">';
        echo '<ul class="wpr-main-tab">';
        if ($wp_reset_licensing->is_active()) {
            echo '<li><a href="#tab-reset">' . __('Reset', 'wp-reset') . '</a></li>';
            echo '<li><a href="#tab-tools">' . __('Tools', 'wp-reset') . '</a></li>';
            echo '<li><a href="#tab-snapshots">' . __('Snapshots', 'wp-reset') . '</a></li>';
            echo '<li><a href="#tab-collections">' . __('Collections', 'wp-reset') . '</a></li>';
            echo '<li><a href="#tab-settings">' . __('Settings', 'wp-reset') . '</a></li>';
            echo '<li><a href="#tab-support">' . __('Support', 'wp-reset') . '</a></li>';
        }
        if (WP_Reset_Utility::whitelabel_filter()) {
            echo '<li><a href="#tab-license">' . __('License', 'wp-reset') . '</a></li>';
        }

        if ($options['debug'] == true) {
            echo '<li><a href="#tab-debug"><span style="color: #dd3036; line-height: 32px;" class="dashicons dashicons-admin-tools"></span> ' . __('Debug', 'wp-reset') . '</a></li>';
        }

        echo '</ul>';
        echo '</div>'; // container
        echo '</nav>';

        echo '<div id="wpr-notifications">';
        echo '<div class="wpr-container">';
        $this->custom_notifications();
        echo '</div>';
        echo '</div>'; // wpr-notifications

        // tabs
        echo '<div class="wpr-container">';
        echo '<div id="wpr-content">';

        if ($wp_reset_licensing->is_active()) {
            echo '<div style="display: none;" id="tab-reset">';
            $this->tab_reset();
            echo '</div>';

            echo '<div style="display: none;" id="tab-tools">';
            $this->tab_tools();
            echo '</div>';

            echo '<div style="display: none;" id="tab-snapshots">';
            $this->tab_snapshots();
            echo '</div>';

            echo '<div style="display: none;" id="tab-collections">';
            $wp_reset_collections->tab_collections();
            echo '</div>';
        }

        echo '<div style="display: none;" id="tab-settings">';
        $this->tab_settings();
        echo '</div>';

        echo '<div style="display: none;" id="tab-support">';
        $this->tab_support();
        echo '</div>';

        if (WP_Reset_Utility::whitelabel_filter()) {
            echo '<div style="display: none;" id="tab-license">';
            $this->tab_license();
            echo '</div>';
        }

        if ($options['debug'] == true) {
            echo '<div style="display: none;" id="tab-debug">';
            $this->tab_debug();
            echo '</div>';
        }

        echo '</div>'; // content
        echo '</div>'; // container
        echo '</div>'; // wp-reset-tabs

        echo '</div>'; // wrap
    } // plugin_page


    /**
     * Echoes all custom plugin notitications
     *
     * @return null
     */
    private function custom_notifications()
    {
        $notice_shown = false;
    } // custom_notifications


    /**
     * Echoes content for log tab
     *
     * @return null
     */
    private function tab_debug()
    {
        global $wpdb;
        echo '<div class="card">';
        echo '<h4><span class="card-name">WP Reset Debug</span><div class="card-header-right"></div></h4>';
        echo '<div class="card-body">';
        echo '<p class="red">Only use the Debug Mode if you know what you are doing or have been instructed to do so by support. While debug mode is enabled, any PHP notices and errors will be printed in AJAX responses, so you should not leave the Debug mode enabled during normal use.</p>';
        echo '<p><a href="' . admin_url('tools.php?page=wp-reset&wpr_debug=false') . '" class="button button-delete">Disable debug mode</a></p>';

        echo '</p></div>';
        echo '</div>';

        echo '<div class="card">';
        echo '<h4><span class="card-name">Log</span><div class="card-header-right"></div></h4>';
        echo '<div class="card-body">';
        
        echo '<div class="wpr-log-wrapper">';
        $log = $this->get_log();
        if (count($log) == 0) {
            echo 'No log entries';
        } else {
            $log = array_reverse($log, true);
            echo '<ul>';
            foreach ($log as $id => $message) {
                echo '<li class="wpr-log-message"><strong>[' . date('Y-m-d H:i:s', $message['time']) . ']</strong> <span class="wpr-log-message-' . $message['type'] . '">' . $message['message'] . '</span></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        echo '<p><a class="button button-delete" href="' . add_query_arg(array('action' => 'wpr_clear_log', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '">Clear Log</a>';
        //echo '<p><a class="button button-delete" href="' . add_query_arg(array('action' => 'wpr_clear_autouploader', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '">Clear Autouploader</a>';
        
        echo '</div>';
        echo '</div>';

        //Cleanup files
        echo '<div class="card">';
        echo '<h4><span class="card-name">WP Reset Files</span><div class="card-header-right"></div></h4>';
        echo '<div class="card-body"><p>';

        $total_files = 0;
        $total_size = 0;
        $files = $this->scan_folder($this->export_dir_path());
        if(empty($files)){
            echo 'There are 0 temporary files, totaling 0 bytes';
        } else {
            foreach($files as $file){
                if(stripos($file, '.htaccess')){
                    continue;
                }
                $total_files++;
                $total_size+=filesize($file);
            }
            
            echo 'There are ' . $total_files . ' temporary files, totaling ' . WP_Reset_Utility::format_size($total_size);
            echo '<p><a class="button button-delete" href="' . add_query_arg(array('action' => 'wpr_delete_temporary_files', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '">Delete temporary snapshot files</a></p>';
        }

        echo '</p></div>';
        echo '</div>';

        //Orphaned snapshot tables
        echo '<div class="card">';
        echo '<h4><span class="card-name">Snapshots</span><div class="card-header-right"></div></h4>';
        echo '<div class="card-body"><p>';

        $snapshots = $this->get_snapshots();
        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        $uid_length = 7;
        if (strlen($wpdb->prefix) > 10) {
            $uid_length = 5;
        }

        $orphaned_snapshots = array();

        foreach ($tables as $table) {
            if ($uid_length !== stripos($table[0], $wpdb->prefix)) {
                continue;
            }
            $current_id = substr($table[0], 0, ($uid_length-1));

            if(array_key_exists($current_id, $snapshots)){
                continue;
            }

            if(!array_key_exists($current_id, $orphaned_snapshots)){
                $orphaned_snapshots[$current_id] = array();
            }
            $orphaned_snapshots[$current_id][] = $table[0];
        } // foreach

        if(empty($orphaned_snapshots)){
            echo 'There are no orphaned snapshot tables';
        } else {
            echo '<p>The tables below <strong>appear</strong> to have belonged to snapshots that have been deleted. They could have remained behind because of a timeout or other errors when deleting the associated snapshot.</p>';
            echo '<p class="red">Double check the tables names and make sure you want to delete them. There is NO UNDO!</p>';
            foreach($orphaned_snapshots as $uid => $snapshot){
                if(array_key_exists($uid, $snapshots)){
                    continue;
                }

                echo '<hr />';
                echo '<div>';
                echo 'Prefix/Snapshot ID: <strong>' . $uid . '</strong><br />';
                echo '<ul>';
                echo '<li>Tables:</li>';
                foreach($snapshot as $table){
                    echo '<li>' . $table . '</li>';
                }
                echo '</ul>';
                echo '<a class="button button-delete" href="' . add_query_arg(array('action' => 'wpr_delete_snapshot_tables', 'uid' => $uid, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '">Delete the tables prefixed with ' . $uid . '_</a>';
                echo '</div>';
            }
        }
        
        echo '</p></div>';
        echo '</div>';
    }


    /**
     * Echoes content for license tab
     *
     * @return null
     */
    private function tab_license()
    {
        global $wp_reset_licensing;
        $options = $this->get_license();

        echo '<div class="card">';
        echo '<h4><span class="card-name">License</span><div class="card-header-right"></div></h4>';
        echo '<div class="card-body">';
        echo '<p>Your License Key: 798af5ec-37694bca-8b5944cd-3a583792. Enter this key in the license key field. Click the Save & Activate License button.By NullMaster.<br>
    If you don\'t have a license - <a target="_blank" href="' . $this->generate_web_link('license-tab') . '">purchase one now</a>. In case of problems please <a href="#" class="open-beacon">contact support</a>.</p>';
        echo '<p>You can manage your licenses in the <a target="_blank" href="' . $this->generate_dashboard_link('license-tab') . '">WP Reset Dashboard</a></p>';
        echo '<table class="form-table"><tbody><tr>
    <th scope="row"><label for="license-key">License Key</label></th>
    <td>
    <input class="regular-text" type="text" id="license-key" value="" placeholder="' . (empty($options['license_key']) ? '12345678-12345678-12345678-12345678' : substr(esc_attr($options['license_key']), 0, 8) . '-************************') . '">
    </td></tr>';

        echo '<tr><th scope="row"><label for="">' . __('License Status', 'wp-reset') . '</label></th><td>';
        if ($wp_reset_licensing->is_active()) {
            $license_formatted = $wp_reset_licensing->get_license_formatted();
            echo '<b style="color: #66b317;">Active</b><br>
        Type: ' . $license_formatted['name_long'];
            echo '<br>Valid ' . $license_formatted['valid_until'] . '</td>';
        } else { // not active
            echo '<strong style="color: #ea1919;">Inactive</strong>';
            if (!empty($wp_reset_licensing->get_license('error'))) {
                echo '<br>Error: ' . $wp_reset_licensing->get_license('error');
            }
        }
        echo '</td></tr>';

        echo '<tr><td colspan="2"><br>';
        echo '<a href="#" id="save-license" data-text-wait="Validating. Please wait." class="button button-primary">Save &amp; Activate License</a>';

        if ($wp_reset_licensing->is_active()) {
            echo '&nbsp; &nbsp;<a href="#" id="deactivate-license" class="button button-delete">Deactivate License</a>';
        } else {
            echo '&nbsp; &nbsp;<a href="#" class="button button-primary" data-text-wait="Validating. Please wait." id="wpr_keyless_activation">Keyless Activation</a>';
        }
        echo '</td></tr>';
        echo '</tbody></table>';

        echo '</div>';
        echo '</div>';

        if ($wp_reset_licensing->is_active('white_label')) {
            echo '<div class="card">';
            echo '<h4><span class="card-name">White-Label License Mode</span><div class="card-header-right"></div></h4>';
            echo '<div class="card-body">';
            echo '<p>Enabling the white-label license mode hides the License tab, removes everything except the ERS from the Support tab, and removes all visible mentions of WebFactory Ltd.<br>To disable it append <strong>&amp;wpr_wl=false</strong> to the WP Reset settings page URL.
          Or save this URL and open it when you want to disable the white-label license mode:<br> <a href="' . admin_url('tools.php?page=wp-reset&wpr_wl=false') . '">' . admin_url('tools.php?page=wp-reset&wpr_wl=false') . '</a></p>';
            echo '<p><a href="' . admin_url('tools.php?page=wp-reset&wpr_wl=true') . '" class="button button-secondary">Enable White-Label License Mode</a></p>';
            echo '</div>';
            echo '</div>';
        }
    } // tab_license


    /**
     * Echoes content for reset tab
     *
     * @return null
     */
    private function tab_reset()
    {
        global $wpdb;
        $current_user = wp_get_current_user();

        echo '<div class="card">';
        echo $this->get_card_header(__('Please read carefully before proceeding', 'wp-reset'), 'reset-description', array('collapse_button' => true));
        echo '<div class="card-body">';
        echo '<p>The following table details what data will be deleted (reset or destroyed) when a selected reset tool is run. Please read it! ';
        if (WP_Reset_Utility::whitelabel_filter()) {
            echo 'If something is not clear <a href="#" class="open-beacon">contact support</a> before running any tools. It\'s better to ask than to be sorry!';
        }
        echo '</p>';
        echo '<p><i class="dashicons dashicons-trash red" style="vertical-align: bottom;"></i> - tool WILL delete, reset or destroy the noted data<br>';
        echo '<i class="dashicons dashicons-yes" style="vertical-align: bottom;"></i> - tool will not touch the noted data in any way</p>';

        echo '<table id="reset-details" class="">';
        echo '<tr>';
        echo '<th>&nbsp;</th>';
        echo '<th>Options Reset</th>';
        echo '<th>Site Reset</th>';
        echo '<th>Nuclear Reset</th>';
        echo '</tr>';
        $rows = array();
        $rows['Posts, pages &amp; custom post types'] = array(0, 1, 1);
        $rows['Comments'] = array(0, 1, 1);
        $rows['Media'] = array(0, 1, 1);
        $rows['Media files'] = array(0, 0, 1);
        $rows['Users'] = array(0, 1, 1);
        $rows['Current user - ' . $current_user->user_login] = array(0, 0, 0);
        $rows['Widgets'] = array(1, 1, 1);
        $rows['Transients'] = array(1, 1, 1);
        $rows['Settings &amp; Options (from WP, plugins &amp; themes)'] = array(1, 1, 1);
        $rows['Site title, WP address, site address,<br>search engine visibility, timezone'] = array(0, 0, 0);
        $rows['Site language'] = array(0, 0, 1);
        $rows['Data in all default WP tables'] = array(0, 1, 1);
        $rows['Custom database tables with prefix ' . $wpdb->prefix] = array(0, 1, 1);
        $rows['Other database tables'] = array(0, 0, 0);
        $rows['Plugin Files'] = array(0, 0, 1);
        $rows['MU Plugin Files'] = array(0, 0, 1);
        $rows['Drop-in Files'] = array(0, 0, 1);
        $rows['Theme Files'] = array(0, 0, 1);
        $rows['All files in uploads'] = array(0, 0, 1);
        $rows['Custom folders in wp-content'] = array(0, 0, 1);

        foreach ($rows as $tool => $opt) {
            echo '<tr>';
            echo '<td>' . $tool . '</td>';
            if (empty($opt[0])) {
                echo '<td><i class="dashicons dashicons-yes" title="Data will NOT be deleted, reset or modified" data-tooltip="Data will NOT be deleted, reset or modified"></i></td>';
            } else {
                echo '<td><i class="dashicons dashicons-trash red" title="Data WILL BE deleted, reset or modified" data-tooltip="Data WILL BE deleted, reset or modified"></i></td>';
            }
            if (empty($opt[1])) {
                echo '<td><i class="dashicons dashicons-yes" title="Data will NOT be deleted, reset or modified" data-tooltip="Data will NOT be deleted, reset or modified"></i></td>';
            } else {
                echo '<td><i class="dashicons dashicons-trash red" title="Data WILL BE deleted, reset or modified" data-tooltip="Data WILL BE deleted, reset or modified"></i></td>';
            }
            if (empty($opt[2])) {
                echo '<td><i class="dashicons dashicons-yes" title="Data will NOT be deleted, reset or modified" data-tooltip="Data will NOT be deleted, reset or modified"></i></td>';
            } else {
                echo '<td><i class="dashicons dashicons-trash red" title="Data WILL BE deleted, reset or modified" data-tooltip="Data WILL BE deleted, reset or modified"></i></td>';
            }
            echo '</tr>';
        } // foreach $rows
        echo '</table>';

        echo '<p><b>' . __('What happens when I run any Reset tool?', 'wp-reset') . '</b></p>';
        echo '<ul class="plain-list">';
        echo '<li>' . __('remember, always <b>make a backup</b> first', 'wp-reset') . '</li>';
        echo '<li>' . __('you will have to confirm the action one more time', 'wp-reset') . '</li>';
        echo '<li>' . __('see the table above to find out what exactly will be reset or deleted', 'wp-reset') . '</li>';
        echo '<li>' . __('site title, WordPress URL, site URL, site language, search engine visibility and current user will always be restored', 'wp-reset') . '</li>';
        echo '<li>' . __('you will be logged out, automatically logged back in and taken to the admin dashboard', 'wp-reset') . '</li>';
        echo '<li>' . __('WP Reset plugin will be reactivated if that option is chosen', 'wp-reset') . '</li>';
        echo '</ul>';

        echo '<p><b>' . __('WP-CLI Support', 'wp-reset') . '</b><br>';
        echo '' . sprintf(__('All tools available via GUI are available in WP-CLI as well. To get the list of commands run %s. Instead of the active user, the first user with admin privileges found in the database will be restored. ', 'wp-reset'), '<code>wp help reset</code>');
        echo sprintf(__('All actions have to be confirmed. If you want to skip confirmation use the standard %s option. Please be careful and backup first.', 'wp-reset'), '<code>--yes</code>') . '</p>';

        echo '</p></div></div>'; // card description

        $theme =  wp_get_theme();
        $theme_name = $theme->get('Name');
        if (empty($theme_name)) {
            $theme_name = '<i>no active theme</i>';
        }
        $active_plugins = get_option('active_plugins');

        // options reset
        echo '<div class="card">';
        echo $this->get_card_header(__('Options Reset', 'wp-reset'), 'tool-options-reset', array('collapse_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>Options table will be reset to default values meaning all WP core settings, widgets, theme settings and customizations, and plugin settings will be gone. Other content and files will not be touched including posts, pages, custom post types, comments and other data stored in separate tables. Site URL and name will be kept as well. Please see the <a href="#reset-details" class="scrollto">table above</a> for details.</p>';

        echo $this->get_tool_icons(false, true);

        echo '<p><br><label for="reset-options-reactivate-theme"><input type="checkbox" id="reset-options-reactivate-theme" value="1"> ' . __('Reactivate current theme', 'wp-reset') . ' - ' . $theme_name . '</label></p>';
        echo '<p><label for="reset-options-reactivate-plugins"><input type="checkbox" id="reset-options-reactivate-plugins" value="1"> Reactivate ' . sizeof($active_plugins) . ' currently active plugin' . (sizeof($active_plugins) != 1 ? 's' : '') . ' (WP Reset will reactivate by default)</label></p>';

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to reset all options?" data-btn-confirm="Reset all options" data-text-wait="Resetting options. Please wait." data-text-confirm="All options stored in the WP options table will be reset.' . $this->get_autosnapshot_tools_modal('Before running the options reset tool') . '" data-text-done="All options have been reset. Reload the page to see changes." data-text-done-singular="All options have been reset. Reload the page to see changes." class="button button-delete" href="#" id="reset-options">Reset all options</a></p>';
        echo '</div>';
        echo '</div>'; // options reset

        // reset
        echo '<div class="card">';
        echo $this->get_card_header(__('Site Reset', 'wp-reset'), 'tool-site-reset', array('collapse_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>WordPress will be reset and returned to default settings. All data stored in the database will be erased including all content such as posts, pages and media. No files will be deleted or modified. Please see the <a href="#reset-details" class="scrollto">table above</a> for details.</p>';

        echo $this->get_tool_icons(false, true);

        echo '<p><br><label for="site-reset-reactivate-theme"><input type="checkbox" id="site-reset-reactivate-theme" value="1"> ' . __('Reactivate current theme', 'wp-reset') . ' - ' . $theme_name . '</label></p>';
        echo '<p><label for="site-reset-reactivate-wpreset"><input type="checkbox" id="site-reset-reactivate-wpreset" value="1" checked> ' . __('Reactivate WP Reset plugin', 'wp-reset') . '</label></p>';
        echo '<p><label for="site-reset-reactivate-plugins"><input type="checkbox" id="site-reset-reactivate-plugins" value="1"> ' . __('Reactivate all currently active plugins', 'wp-reset') . '</label></p>';

        echo '<p>' . __('Type <b>reset</b> in the confirmation field to confirm the reset and then click the "Reset WordPress" button.', 'wp-reset') . '</p>';
        echo '<p><input id="site_reset_confirm" type="text" placeholder="' . esc_attr__('Type in "reset"', 'wp-reset') . '" value="" autocomplete="off"> &nbsp;';
        echo '<a data-confirm-title="Are you sure you want to reset the site?" data-text-wait="Resetting site. Please wait." data-text-confirm="The site will be reset to default values. All content will be deleted.' . $this->get_autosnapshot_tools_modal('Before running the site reset tool') . '" data-text-done="Done. Page will reload in a few seconds." class="button button-delete" href="#" id="site-reset">' . __('Reset Site', 'wp-reset') . '</a></p>';
        echo '</div>';
        echo '</div>'; // reset

        // nuclear reset
        echo '<div class="card">';
        echo $this->get_card_header(__('Nuclear Site Reset', 'wp-reset'), 'tool-nuclear-reset', array('collapse_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All data will be deleted or reset (see the <a href="#reset-details" class="scrollto">explanation table</a> for details). All data stored in the database including custom tables with <code>' . $wpdb->prefix . '</code> prefix, as well as all files in wp-content, themes and plugins folders. The only thing restored after reset will be your user account so you can log in again, and the basic WP settings like site URL. Please see the <a href="#reset-details" class="scrollto">table above</a> for details.</p>';

        echo $this->get_tool_icons(true, true);

        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would delete files shared by multiple sites in the WP network.</p>';
        } else {
            echo '<p><br><label for="nuclear-reset-reactivate-wpreset"><input type="checkbox" id="nuclear-reset-reactivate-wpreset" value="1" checked> ' . __('Reactivate WP Reset plugin', 'wp-reset') . '</label></p>';

            echo '<p>' . __('Type <b>reset</b> in the confirmation field to confirm the reset and then click the "Reset WordPress &amp; Delete All Custom Files &amp; Data" button. <b>There is NO UNDO.', 'wp-reset') . '</b></p>';

            echo '<p class="mb0"><input id="nuclear_reset_confirm" type="text" placeholder="' . esc_attr__('Type in "reset"', 'wp-reset') . '" value="" autocomplete="off"> &nbsp;';
            echo '<a data-confirm-title="Are you sure you want to reset WordPress and delete all custom files?" data-btn-confirm="Reset WP &amp; Delete All Custom Files &amp; Data" data-text-wait="Resetting. Please wait." data-text-confirm="WordPress will be completely reset and all files (plugins, themes, uploads) deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="Done. Page will reload in a few seconds." class="button button-delete" href="#" id="nuclear-reset">' . __('Reset WordPress &amp; Delete All Custom Files &amp; Data', 'wp-reset') . '</a></p>';
        }
        echo '</div>';
        echo '</div>'; // nuclear reset
    } // tab_reset


    /**
     * Echoes content for tools tab
     *
     * @return null
     */
    private function tab_tools()
    {
        global $wpdb, $wp_version, $wp_reset_tools;
        $tools = array(
            'tool-reset-theme-options' => 'Reset Theme Options',
            'tool-delete-transients' => 'Delete Transients',
            'tool-purge-cache' => 'Purge Cache',
            'tool-delete-local-data' => 'Delete Local Data',
            'tool-reset-user-roles' => 'Reset User Roles',
            'tool-delete-content' => 'Delete Content',
            'tool-delete-widgets' => 'Delete Widgets',
            'tool-delete-themes' => 'Delete Themes',
            'tool-delete-plugins' => 'Delete Plugins',
            'tool-delete-mu-plugins-dropins' => 'Delete MU Plugins &amp; Drop-ins',
            'tool-delete-uploads' => 'Clean Uploads Folder',
            'tool-delete-wp-content' => 'Clean wp-content Folder',
            'tool-empty-delete-custom-tables' => 'Empty or Delete Custom Tables',
            'tool-switch-wp-version' => 'Switch WP Version',
            'tool-delete-htaccess' => 'Delete .htaccess File'
        );

        echo '<div class="card">';
        echo $this->get_card_header(__('Index of Tools', 'wp-reset'), 'iot', array('collapse_button' => true));
        echo '<div class="card-body">';
        $i = 0;
        $tools_nb = sizeof($tools);
        foreach ($tools as $tool_id => $tool_name) {
            if ($i == 0) {
                echo '<div class="third">';
                echo '<ul class="mb0 plain-list">';
            }
            if ($i == 5 || $i == 10) {
                echo '</div>';
                echo '<div class="third">';
                echo '<ul class="mb0 plain-list">';
            }

            echo '<li><a title="Jump to ' . $tool_name . ' tool" class="scrollto" href="#' . $tool_id . '">' . $tool_name . '</a></li>';

            if ($i == $tools_nb - 1) {
                echo '</ul>';
                echo '</div>'; // third
            }
            $i++;
        } // foreach tools
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Reset Theme Options', 'wp-reset'), 'tool-reset-theme-options', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>' . __('All options (mods) for all themes will be reset; not just for the active theme. The tool works only for themes that use the <a href="https://codex.wordpress.org/Theme_Modification_API" target="_blank">WordPress theme modification API</a>. If options are saved in some other custom way they won\'t be reset.', 'wp-reset') . '</p>';

        echo $this->get_tool_icons(false, true);

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to reset all theme options?" data-btn-confirm="Reset theme options" data-text-wait="Resetting theme options. Please wait." data-text-confirm="All options (mods) for all themes will be reset.' . $this->get_autosnapshot_tools_modal('Before running the reset theme options tool') . '" data-text-done="Options for %n themes have been reset." data-text-done-singular="Options for one theme have been reset." class="button button-delete" href="#" id="reset-theme-options">Reset theme options</a></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Transients', 'wp-reset'), 'tool-delete-transients', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All transient related database entries will be deleted. Including expired and non-expired transients, and orphaned transient timeout entries.</p>';

        echo $this->get_tool_icons(false, true);

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all transients?" data-btn-confirm="Delete all transients" data-text-wait="Deleting transients. Please wait." data-text-confirm="All database entries related to transients will be deleted.' . $this->get_autosnapshot_tools_modal('Before running the delete transients tool') . '" data-text-done="%n transient database entries have been deleted." data-text-done-singular="One transient database entry has been deleted." class="button button-delete" href="#" id="delete-transients">Delete all transients</a></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Purge Cache', 'wp-reset'), 'tool-purge-cache', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All cache objects stored in both files and the database will be deleted. Along with WP object cache and transients, cache from the following plugins will be purged: W3 Total Cache, WP Cache, LiteSpeed Cache, Endurance Page Cache, SiteGround Optimizer, WP Fastest Cache and Swift Performance.</p>';

        echo $this->get_tool_icons(true, true);

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to purge all cache?" data-btn-confirm="Purge cache" data-text-wait="Purging cache. Please wait." data-text-confirm="All cache objects will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="Cache has been purged." data-text-done-singular="Cache has been purged." class="button button-delete" href="#" id="purge-cache">Purge cache</a></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Local Data', 'wp-reset'), 'tool-delete-local-data', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All local storage and session storage data will be deleted. Cookies without a custom set path will be deleted as well. WP cookies are not touched, with Delete Local Data button.<br>Deleting all WordPress cookies (including authentication cookies) will delete all WP related cookies and user (you) will be logged out on the next page reload.
    </p>';

        echo $this->get_tool_icons(false, false);

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all local data?" data-btn-confirm="Delete local data" data-text-wait="Deleting local data. Please wait." data-text-confirm="All local data; cookies, local storage and local session will be deleted. There is NO UNDO. WP Reset does not make backups of local data." data-text-done="%n local data objects have been deleted." data-text-done-singular="One local data object has been deleted." class="button button-delete" href="#" id="delete-local-data">Delete local data</a><a data-confirm-title="Are you sure you want to delete all WP related cookies?" data-btn-confirm="Delete all WordPress cookies" data-text-wait="Deleting WP cookies. Please wait." data-text-confirm="All WP cookies including authentication ones will be deleted. You will have to log in again. There is NO UNDO. WP Reset does not make backups of cookies." data-text-done="All WP cookies have been deleted." data-text-done-singular="All WP cookies been deleted." class="button button-delete" href="#" id="delete-wp-cookies">Delete all WordPress cookies</a></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Reset User Roles', 'wp-reset'), 'tool-reset-user-roles', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>Default user roles\' capabilities will be reset to their default values. All custom roles will be deleted.<br>Users that had custom roles will not be assigned any default ones and might not be able to log in. Roles have to be (re)assigned to them manually.</p>';
        echo $this->get_tool_icons(false, true);

        echo '<p class="mb0">';
        echo '<a data-confirm-title="Are you sure you want to reset your user roles\' capabilities and delete all custom roles?" data-text-wait="Resetting user roles. Please wait." data-text-confirm="Default user roles\' capabilities will be reset to their default values. All custom roles will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done-singular="User roles have been reset." class="button button-delete" href="#" id="reset-user-roles">Reset user roles</a>';
        echo '</p>';

        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Content', 'wp-reset'), 'tool-delete-content', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>Besides content, all linked or child records (for selected content) will be deleted to prevent creating orphaned rows in the database. For instance, for posts that\'s posts, post meta, and comments related to posts. Delete process does not call any WP hooks such as <i>before_delete_post</i>. Choosing a post type or taxonomy does not delete that parent object it deletes the child objects. Parent objects are defined in code. If you want to remove them, remove their code definition. When media is deleted, files are left in the uploads folder. To delete files use the <a class="scrollto" href="#tool-delete-uploads">Clean Uploads Folder</a> tool. Deleting users does not affect the current, logged in user account. All orphaned objects will be reassigned to him.</p>';

        echo $this->get_tool_icons(false, true);

        $post_types = get_post_types('', false, 'and');
        $taxonomies = get_taxonomies('', false, 'and');

        echo '<p><select size="6" multiple id="delete-content-types">';
        echo '<option value="_comments">Comments (' . ((int) $wpdb->get_var("SELECT COUNT(comment_id) FROM $wpdb->comments")) . ')</option>';
        echo '<option value="_users">Users (' . ((int) $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->users")) . ')</option>';
        foreach ($post_types as $type) {
            $count = wp_count_posts($type->name, 'readable');
            $tmp = 0;
            foreach ($count as $cnt) {
                $tmp += (int) $cnt;
            }
            echo '<option value="' . $type->name . '">Post type - ' . $type->label . ' (' . $tmp . ')</option>';
        } // foreach post types
        foreach ($taxonomies as $tax) {
            echo '<option value="_tax_' . $tax->name . '">Taxonomy - ' . $tax->label . ' (' . wp_count_terms($tax->name) . ')</option>';
        } // foreach post types

        echo '</select><br>';
        echo 'Select content object(s) you want to delete. Use ctrl + click to select multiple objects.</p>';

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all content for the selected post types?" data-text-wait="Deleting content. Please wait." data-text-confirm="All content and its metadata for the selected post types will be deleted.' . $this->get_autosnapshot_tools_modal('Before running the delete content tool') . '" data-text-done="%n objects have been deleted." data-text-done-singular="One object has been deleted." class="button button-delete" href="#" id="delete-content">Delete content</a></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Widgets', 'wp-reset'), 'tool-delete-widgets', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All widgets, orphaned, active and inactive ones, as well as widgets in active and inactive sidebars will be deleted including their settings. After deleting, WordPress will automatically recreate default, empty database entries related to widgets. So, no matter how many times users run the tool it will never return "no data deleted". That\'s expected and normal.</p>';

        echo $this->get_tool_icons(false, true);

        echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all widgets?" data-btn-confirm="Delete widgets" data-text-wait="Deleting widgets. Please wait." data-text-confirm="All widgets, active and inactive ones will be deleted.' . $this->get_autosnapshot_tools_modal('Before running the delete widgets tool') . '" data-text-done="%n database rows related to widgets have been deleted." data-text-done-singular="One database row related to widgets has been deleted." class="button button-delete" href="#" id="delete-widgets">Delete widgets</a></p>';
        echo '</div>';
        echo '</div>';

        $theme =  wp_get_theme();

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Themes', 'wp-reset'), 'tool-delete-themes', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would delete themes for all sites in the network since they all share the same theme files.</p>';
        } else {
            echo '<p>' . __('All themes will be deleted. Including the currently active theme - ' . $theme->get('Name') . '.', 'wp-reset') . '</p>';

            echo $this->get_tool_icons(true, true);

            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all themes?" data-btn-confirm="Delete all themes" data-text-wait="Deleting all themes. Please wait." data-text-confirm="All themes will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n themes have been deleted." data-text-done-singular="One theme has been deleted." class="button button-delete" href="#" id="delete-themes">Delete all themes</a></p>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete Plugins', 'wp-reset'), 'tool-delete-plugins', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would delete plugins for all sites in the network since they all share the same plugin files.</p>';
        } else {
            echo '<p>' . __('All plugins will be deleted except for WP Reset which will remain active.</b>', 'wp-reset') . '</p>';

            echo $this->get_tool_icons(true, true);

            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all plugins?" data-btn-confirm="Delete plugins" data-text-wait="Deleting plugins. Please wait." data-text-confirm="All plugins except WP Reset will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n plugins have been deleted." data-text-done-singular="One plugin has been deleted." class="button button-delete" href="#" id="delete-plugins">Delete plugins</a></p>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete MU Plugins & Drop-ins', 'wp-reset'), 'tool-delete-mu-plugins-dropins', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>MU Plugins are located in <code>/wp-content/mu-plugins/</code> and are, as the name suggests, must-use plugins that are automatically activated by WP and can\'t be deactiavated via the <a href="' . admin_url('plugins.php?plugin_status=mustuse') . '" target="_blank">plugins interface</a>, although if any are used, they are listed in the "Must Use" tab.<br>';
        echo 'Drop-ins are pieces of code found in <code>/wp-content/</code> that replace default, built-in WordPress functionality. Most often used are <code>db.php</code> and <code>advanced-cache.php</code> that implement custom DB and cache functionality. They can\'t be deactivated via the <a href="' . admin_url('plugins.php?plugin_status=dropins') . '" target="_blank">plugins interface</a> but if any are present are listed in the "Drop-in" tab.</p>';

        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would delete plugins for all sites in the network since they all share the same plugin files.</p>';
        } else {
            echo $this->get_tool_icons(true, false, true);

            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all must use plugins?" data-btn-confirm="Delete must use plugins" data-text-wait="Deleting Must Use plugins. Please wait." data-text-confirm="All must use plugins will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n must use plugins have been deleted." data-text-done-singular="One must use plugin has been deleted." class="button button-delete" href="#" id="delete-mu-plugins">Delete must use plugins</a><a data-confirm-title="Are you sure you want to delete all drop-ins?" data-btn-confirm="Delete drop-ins" data-text-wait="Deleting drop-ins. Please wait." data-text-confirm="All drop-ins will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n drop-ins have been deleted." data-text-done-singular="One drop-in has been deleted." class="button button-delete" href="#" id="delete-dropins">Delete drop-ins</a></p>';
        }
        echo '</div>';
        echo '</div>';

        $upload_dir = wp_upload_dir(date('Y/m'), true);
        $upload_dir['basedir'] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $upload_dir['basedir']);

        echo '<div class="card">';
        echo $this->get_card_header(__('Clean Uploads Folder', 'wp-reset'), 'tool-delete-uploads', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All files in <code>' . $upload_dir['basedir'] . '</code> folder will be deleted. Including folders and subfolders, and files in subfolders. Files associated with <a href="' . admin_url('upload.php') . '">media</a> entries will be deleted too.</p>';

        echo $this->get_tool_icons(true, false);

        if (false != $upload_dir['error']) {
            echo '<p class="mb0"><span style="color:#dd3036;"><b>Tool is not available.</b></span> Folder is not writeable by WordPress. Please check file and folder access rights.</p>';
        } else {
            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all files &amp; folders in uploads folder?" data-btn-confirm="Delete everything in uploads folder" data-text-wait="Deleting uploads. Please wait." data-text-confirm="All files and folders in uploads will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n files &amp; folders have been deleted." data-text-done-singular="One file or folder has been deleted." class="button button-delete" href="#" id="delete-uploads">Delete all files &amp; folders in uploads folder</a></p>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Clean wp-content Folder', 'wp-reset'), 'tool-delete-wp-content', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>All folders and their content in <code>wp-content</code> folder except the following ones will be deleted: <code>mu-plugins</code>, <code>plugins</code>, <code>themes</code>, <code>uploads</code>, <code>wp-reset-autosnapshots</code>, <code>wp-reset-snapshots-export</code>.</p>';

        echo $this->get_tool_icons(true, false);

        if (false === is_writable(trailingslashit(WP_CONTENT_DIR))) {
            echo '<p class="mb0"><span style="color:#dd3036;"><b>Tool is not available.</b></span> Folder is not writeable by WordPress. Please check file and folder access rights.</p>';
        } else {
            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete all folders in wp-content folder?" data-btn-confirm="Delete folders in wp-content folder" data-text-wait="Cleaning wp-content. Please wait." data-text-confirm="All folders in wp-content will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="%n files &amp; folders have been deleted." data-text-done-singular="One folder or folder has been deleted." class="button button-delete" href="#" id="delete-wp-content">Clean wp-content folder</a></p>';
        }
        echo '</div>';
        echo '</div>';

        $custom_tables = $this->get_custom_tables(true);

        echo '<div class="card">';
        echo $this->get_card_header(__('Empty or Delete Custom Tables', 'wp-reset'), 'tool-empty-delete-custom-tables', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        echo '<p>' . __('This action affects only custom tables with <code>' . $wpdb->prefix . '</code> prefix. Core WP tables and other tables in the database that do not have that prefix will not be deleted/emptied. Deleting (dropping) tables completely removes them from the database. Emptying (truncating) removes all content from them, but keeps the structure intact.</p>', 'wp-reset');

        echo $this->get_tool_icons(false, true, true);

        if ($custom_tables) {
            echo '<p>';
            echo '<select multiple size="6" id="empty-delete-tables-tables">';
            echo '<option value="__all">All custom tables - ' . number_format(sizeof($custom_tables)) . '</value>';
            foreach ($custom_tables as $tbl) {
                echo '<option value="' . $tbl['name'] . '">' . $tbl['name'] . '</value>';
            } // foreach
            echo '</select><br>';
            echo 'Select the table(s) to truncate or delete. Use ctrl + click to select multiple tables.</p>';

            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to empty selected custom tables?" data-btn-confirm="Empty custom tables" data-text-wait="Emptying custom tables. Please wait." data-text-confirm="Selected custom tables will be emptied.' . $this->get_autosnapshot_tools_modal('Before running the empty tables tool') . '" data-text-done="%n custom tables have been emptied." data-text-done-singular="One custom table has been emptied." class="button button-delete" href="#" id="truncate-custom-tables">Empty (truncate) selected custom tables</a>';
            echo '<a data-confirm-title="Are you sure you want to delete selected custom tables?" data-btn-confirm="Delete custom tables" data-text-wait="Deleting custom tables. Please wait." data-text-confirm="Selected custom tables will be deleted.' . $this->get_autosnapshot_tools_modal('Before running the delete tables tool') . '" data-text-done="%n custom tables have been deleted." data-text-done-singular="One custom table has been deleted." class="button button-delete" href="#" id="drop-custom-tables">Delete (drop) selected custom tables</a></p>';
        } else {
            echo '<p>' . __('There are no custom tables. There\'s nothing for this tool to empty or delete.', 'wp-reset') . '</p>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Switch WP Version', 'wp-reset'), 'tool-switch-wp-version', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would change the WP version for all sites in the network since they all share the same core files.</p>';
        } else {
            echo '<p>Replace current WordPress version with the selected new version. Switching from a previous version, to a newer version is mostly supported and properly handled by the WP installer. Reverting WordPress, rolling back WordPress to a previous version is not supported. Results may vary!</p>';

            echo $this->get_tool_icons(true, true);

            $wp_versions = $wp_reset_tools->get_wordpress_versions();
            echo '<label for="select-wp-version">Available WordPress versions:</label> ';
            echo '<select id="select-wp-version">';
            echo '<option value="">select WordPress version</option>';
            foreach ($wp_versions as $version => $release_date) {
                if ($release_date == 'bleeding') {
                    echo '<option value="bleeding">WordPress v' . $version . ' (Bleeding edge nightly)' . ($wp_version == $version ? ' - installed' : '') . '</option>';
                } else if ($release_date == 'point') {
                    echo '<option value="point-' . substr($version, 0, 3) . '">WordPress v' . $version . ' (Point release nightly)' . ($wp_version == $version ? ' - installed' : '') . '</option>';
                } else {
                    echo '<option value="' . $version . '">WordPress v' . $version . ' (' . date('Y-m-d', $release_date) . ')' . ($wp_version == $version ? ' - installed' : '') . '</option>';
                }
            }
            echo '</select>';
            echo ' <a data-text-wait="Refreshing list of WordPress versions. Please Wait" id="refresh-wp-versions" class="button" href="#">Refresh list</a>';

            echo '<p class="mb0">';
            echo '<a data-confirm-title="Are you sure you want to switch the WordPress version?" data-text-wait="Switching WP  version. Please wait." data-text-confirm="This tool will replace your current WordPress installation with the new selected version. There is NO UNDO. WP Reset does not make any file backups." data-text-done="WordPress v%n has been installed. Reload the page to finish the process." class="button button-delete" href="#" id="switch-wp-version">Switch WordPress version</a>';
            echo '</p>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header(__('Delete .htaccess File', 'wp-reset'), 'tool-delete-htaccess', array('collapse_button' => true, 'iot_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';
        if (is_multisite()) {
            echo '<p class="mb0 wpmu-error">This tool is <b>not compatible</b> with WP multisite (WPMU). Using it would change the .htaccess file for all sites in the network since they all use the same .htaccess file.</p>';
        } else {
            echo '<p>' . __('This action deletes the .htaccess file located in <code>' . $this->get_htaccess_path() . '</code></p>', 'wp-reset');

            echo '<p>If you need to edit the .htaccess file, install the free <a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=wp-htaccess-editor&TB_iframe=true&width=600&height=550') . '" class="thickbox open-plugin-details-modal">WP Htaccess Editor</a> plugin. It automatically creates a backup when a user edits .htaccess as well as checks for syntax errors.</p>';

            echo $this->get_tool_icons(true, false);

            echo '<p class="mb0"><a data-confirm-title="Are you sure you want to delete the .htaccess file?" data-btn-confirm="Delete .htaccess file" data-text-wait="Deleting .htaccess file. Please wait." data-text-confirm="Htaccess file will be deleted. There is NO UNDO. WP Reset does not make any file backups." data-text-done="Htaccess file has been deleted." data-text-done-singular="Htaccess file has been deleted." class="button button-delete" href="#" id="delete-htaccess">Delete .htaccess file</a>';
            echo '<a data-confirm-title="Are you sure you want to restore the .htaccess file?" data-btn-confirm="Restore .htaccess file" data-text-wait="Restoring .htaccess file. Please wait." data-text-confirm="Htaccess file will be restored to default. There is NO UNDO. WP Reset does not make any file backups." data-text-done="Htaccess file has been restored." data-text-done-singular="Htaccess file has been restored." class="button" href="#" id="restore-htaccess">Restore .htaccess to default WP values</a></p>';
        }
        echo '</div>';
        echo '</div>';
    } // tab_tools

    /**
     * Echoes content for settings tab
     *
     * @return null
     */
    private function tab_settings()
    {
        $options = $this->get_options();

        echo '<div class="card">';
        echo $this->get_card_header('Cloud', 'cloud', array('collapse_button' => true));
        echo '<div class="card-body">';

        echo '<p>Cloud feature lets you offload snapshots on the selected service to add redundancy and save space on your hosting account. It also stores ZIP files for pro plugins from collections.</p>';
        echo '<div class="sub-option-group">Cloud service: <select id="option_cloud_service">';

        $cloud_options = array_merge(array('none' => 'Disabled'), $this->cloud_services);
        WP_Reset_Utility::create_select_options($cloud_options, $options['cloud_service']);
        echo '</select></div>';

        echo '<p>Currently supported cloud services: WP Reset Cloud, Dropbox, Google Drive, pCloud.<br>';
        echo 'Switching the cloud service will not cause any snapshots or collections to be lost. They will be available when you switch back to the cloud service they are saved on.</p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header('Options', 'snapshots-options', array('collapse_button' => 1));

        echo '<div class="card-body" id="snapshot-options-group">';
        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_tools_snapshots', array('saved_value' => $options['tools_snapshots']));
        echo '<div class="option-group-desc">Automatically create snapshots before running WP Reset tools</div>';
        echo '</div>';
        if (is_multisite()) {
            echo '<p class="wpmu-error mb0">Creating auto snapshots when doing updated and manipulating themes &amp; plugins is <b>not available on WP multisite</b> (WPMU).</p>';
        } else {
            echo '<div class="option-group">';
            WP_Reset_Utility::create_toogle_switch('option_events_snapshots', array('saved_value' => $options['events_snapshots']));
            echo '<div class="option-group-desc">Automatically create snapshots when doing updates and manipulating plugins &amp; themes</div>';
            echo '</div>';
        }

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_snapshots_autoupload', array('saved_value' => $options['snapshots_autoupload']));
        echo '<div class="option-group-desc">Automatically upload user created snapshots to cloud</div>';
        echo '</div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_autosnapshots_autoupload', array('saved_value' => $options['autosnapshots_autoupload']));
        echo '<div class="option-group-desc">Automatically upload automatically created snapshots to cloud</div>';
        echo '</div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_snapshots_upload_delete', array('saved_value' => $options['snapshots_upload_delete']));
        echo '<div class="option-group-desc">Automatically delete snapshots after they are uploaded to cloud</div>';
        echo '</div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_prune_snapshots', array('saved_value' => $options['prune_snapshots'], 'class' => 'has-suboption'));
        echo '<div class="option-group-desc">Automatically delete automatic snapshots</div>';
        $prune_details = array(
            'days-1' => 'older than one day',
            'days-3' => 'older than 3 days',
            'days-5' => 'older than 5 days',
            'days-7' => 'older than 7 days',
            'days-10' => 'older than 10 days',
            'days-15' => 'older than 15 days',
            'days-30' => 'older than 30 days',
            'cnt-10' => 'when there are more than 10 snapshots saved',
            'cnt-20' => 'when there are more than 20 snapshots saved',
            'cnt-50' => 'when there are more than 50 snapshots saved',
            'cnt-100' => 'when there are more than 100 snapshots saved',
            'size-50' => 'when total snapshot size exceeds 50 MB',
            'size-100' => 'when total snapshot size exceeds 100 MB',
            'size-200' => 'when total snapshot size exceeds 200 MB',
            'size-500' => 'when total snapshot size exceeds 500 MB',
            'size-1000' => 'when total snapshot size exceeds 1000 MB',
        );
        echo '<div class="sub-option-group">Delete automatic snapshots: <select id="option_prune_snapshots_details">';
        WP_Reset_Utility::create_select_options($prune_details, $options['prune_snapshots_details']);
        echo '</select></div></div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_ajax_snapshots_export', array('saved_value' => $options['ajax_snapshots_export']));
        echo '<div class="option-group-desc">Export snapshots table by table, slower but can be needed for large databases</div>';
        echo '</div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_adminbar_snapshots', array('saved_value' => $options['adminbar_snapshots']));
        echo '<div class="option-group-desc">Show WP Reset menu to administrators in admin bar</div>';
        echo '</div>';


        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_optimize_tables', array('saved_value' => $options['optimize_tables']));
        echo '<div class="option-group-desc">Optimize tables before creating snapshots (slower but improves accuracy when using the "Compare snapshot to current data" tool)</div>';
        echo '</div>';


        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_throttle_ajax', array('saved_value' => $options['throttle_ajax']));
        echo '<div class="option-group-desc">Throttle AJAX requests (some servers allow only a limited number of requests per second so all snapshot related AJAX requests have to be slowed down)</div>';
        echo '</div>';


        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_fix_datetime', array('saved_value' => $options['fix_datetime']));
        echo '<div class="option-group-desc">Fix mysql zero dates (enable this if you encounter errors related to date columns with zero dates when importing or downloading snapshots from the cloud )</div>';
        echo '</div>';


        echo '<div class="option-group">';
        echo '<label for="size">Display an alert when the total database space used by locally stored snapshots exceeds:</label> <select id="option_snapshots_size_alert">';
        WP_Reset_Utility::create_select_options(array('100' => '100MB', '250' => '250MB', '500' => '500MB', '1000' => '1 GB', '2000' => '2 GB', '0' => 'Disable Alert'), $options['snapshots_size_alert']);
        echo '</select>';
        echo '</div>';

        echo '<div class="option-group">';
        WP_Reset_Utility::create_toogle_switch('option_alternate_db_connection', array('saved_value' => $options['alternate_db_connection']));
        echo '<div class="option-group-desc">Use alternate MySQL connection. Enable if you encounter connection errors exporting/importing snapshots.</div>';
        echo '</div>';


        echo '<hr>';
        echo '<p><a href="#" class="button" id="save-snapshot-options">Save options</a></p>';
        echo '</div>';
        echo '</div>';
    } // tab_settings

    /**
     * Echoes content for support tab
     *
     * @return null
     */
    private function tab_support()
    {
        global $wp_reset_licensing;

        $options = $this->get_options();

        if (!WP_Reset_Utility::whitelabel_filter()) {
            $license = $wp_reset_licensing->get_license();
            if (!empty($license['meta']['white_label_support_text'])) {
                echo '<div class="card">';
                echo $this->get_card_header('Info & Contact', 'custom-support-text', array());
                echo '<div class="card-body">';
                echo '<p>' . $license['meta']['white_label_support_text'] . '</p>';
                echo '</div>';
                echo '</div>';
            }
        }

        echo '<div class="card">';
        echo $this->get_card_header('Emergency Recovery Script', 'emergency-recovery-script', array('collapse_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';

        echo '<p>Emergency recovery script is a standalone, single-file, WP independent script created to recovery a WP site in the most difficult situations. When access to admin is not possible, when core files are compromised (accidental delete or malware), when you get the white-screen or can\'t log in for whatever reason. If WP is running normally, and you can access the admin, then there\'s no reason to use it.</p>';
        echo '<p>There are two ways to use the script: have it ready on the site in case of emergency, or upload it only when needed. On production sites, when big and potentially dangerous changes rarely happen, we suggest uploading it only when needed. On test sites have it ready because there\'s a higher probability that you\'ll need it.</p><hr>';

        $recovery_url = $this->wpr_recovery_path(true);

        if ($recovery_url !== false) {
            $recovery_password = $this->get_recovery_password();
            $recovery_version = $this->get_recovery_version();
            echo '<p><b>Emergency recovery script v' . $recovery_version . ' is enabled</b> and available on the URL below. <b>Do NOT share</b> the URL with anyone. Its name is randomly generated, so nobody except you can get to it. Please bookmark the URL. When WP dies you won\'t be able to open this page.<br><a href="' . $recovery_url . '" target="_blank">' . $recovery_url . '</a></p>';
            echo 'The password for the Emergency recovery script is ' . (!empty($recovery_password) ? '<strong style="color:#F00">' . $recovery_password . '</strong>' : '<strong style="color:#F00">not set</strong>, please remove and reinstall the script to automatically set a random password.') . '</strong>';

            echo '<p>';
            echo '<a class="button button-delete delete-recovery-script" href="#">Remove emergency recovery script</a>';
            if (version_compare($this->er_version, $recovery_version, '>')) {
                echo '<a class="button button-update update-recovery-script" href="#">Update emergency recovery script to v' . $this->er_version . '</a>';
            }
            echo '</p>';
        } else {
            echo '<p>Emergency recovery script is not enabled. You can always get it from the <a href="' . trailingslashit($this->licensing_servers[0]) . 'wpr_recovery_download.php" target="_blank">WP Reset website</a> and upload manually when needed or enable it with the button below so it\'s accessible on a secret URL.</p>';
            echo '<p><a class="button install-recovery-script" href="#">Enable emergency recovery script</a></p>';
        }
        echo '</div>';
        echo '</div>';

        if (WP_Reset_Utility::whitelabel_filter()) {
            echo '<div class="card">';
            echo $this->get_card_header('Documentation', 'documentation-support', array());
            echo '<div class="card-body">';
            echo '<p>' . __('All tools and functions are explained in detail in <a href="' . $this->generate_web_link('support-tab', '/documentation/') . '" target="_blank">the documentation</a>. We did our best to describe how things work on both the code level and an "average user" level. Most tools have direct links (look for the <span class="dashicons dashicons-editor-help"></span> icon) to specific parts of documentation.', 'wp-reset') . '</p>';
            echo '<p>If needed you can always <a href="#" class="open-onboarding">run onboarding</a> again to help you test and setup WP Reset</p>';
            echo '</div>';
            echo '</div>';

            echo '<div class="card">';
            echo $this->get_card_header('Contact Support', 'contact-support', array());
            echo '<div class="card-body">';
            echo '<p>Please don\'t hesitate to get in touch if you need any help. Try to be as detailed as possible so we can provide the best answer in the first reply. If you\'re unable to use our support widget in the lower right corner - <a href="mailto:wpreset@webfactoryltd.com">email us</a>.</p>';
            echo '<p>';
                echo '<a href="#" class="button button-primary open-beacon">Contact Support</a>';
                if ($options['debug'] == true) {
                    echo '<a href="' . admin_url('tools.php?page=wp-reset&wpr_debug=false') . '" class="button button-delete disable-debug-mode">Disable debug mode</a>';
                } else {
                    echo '<a href="' . admin_url('tools.php?page=wp-reset&wpr_debug=true') . '" data-confirm-title="Are you sure you want to enable debug mode?" data-text-confirm="Debug mode will enable the Debug Tools tab which contains the Log and other tools related only to WP Reset. Only use this if you know what you are doing or have been instructed to do so by support. While debug mode is enabled, any PHP notices and errors will be printed in AJAX responses, so you should not leave the Debug mode enabled during normal use." data-btn-confirm="Enable debug mode" class="enable-debug-mode button button-delete">Enable debug mode</a>';
                }
            echo '</p>';
            echo '</div>';
            echo '</div>';
        }
    } // tab_support


    /**
     * Echoes content for snapshots tab
     *
     * @return null
     */
    private function tab_snapshots()
    {
        global $wpdb, $wp_reset_cloud;
        $tbl_core = $tbl_custom = $tbl_size = $tbl_rows = 0;
        $options = $this->get_options();

        echo '<div class="card" id="card-snapshots">';
        echo $this->get_card_header('Snapshots', 'snapshots-info', array('collapse_button' => true, 'documentation_link' => true));
        echo '<div class="card-body">';

        echo '<p>A snapshot is a copy of all WP database tables, standard and custom ones, saved in the site\'s database. <a href="https://www.youtube.com/watch?v=xBfMmS12vMY" target="_blank">Watch a short video</a> overview and tutorial about Snapshots.</p>';

        echo '<p>Snapshots are primarily a development tool. When using various reset tools we advise using our 1-click snapshot tool available in every tool\'s confirmation dialog. If a full backup that includes files is needed, use one of the <a href="' . admin_url('plugin-install.php?s=backup&tab=search&type=term') . '" target="_blank">backup plugins</a> from the repo.</p>';

        echo '<p>Use snapshots to find out what changes a plugin made to your database or to quickly restore the dev environment after testing database related changes. Restoring a snapshot does not affect other snapshots, or WP Reset settings.</p>';

        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);

        if (is_array($tables)) {
            foreach ($tables as $table) {
                if (0 !== stripos($table[0], $wpdb->prefix)) {
                    continue;
                }

                if (in_array($table[0], $this->core_tables)) {
                    $tbl_core++;
                } else {
                    $tbl_custom++;
                }
            } // foreach

            echo '<p><b>Currently used WordPress tables</b>, prefixed with <i>' . $wpdb->prefix . '</i>, consist of ' . $tbl_core . ' standard and ';
            if ($tbl_custom) {
                echo $tbl_custom . ' custom table' . ($tbl_custom == 1 ? '' : 's');
            } else {
                echo 'no custom tables';
            }
            echo ' <span id="wpr-table-details"><a href="#" id="show-table-details">(show details)</a></span>';
        }

        echo '<p>You can configure Snapshot Options in the <a class="change-tab" data-tab="4" href="#snapshot-options-group">Settings</a> tab</p>';

        echo '</div>';
        echo '</div>'; // snapshots desc

        $snapshots = $this->get_all_snapshots();

        $snapshots = array_reverse($snapshots);
        $ss_user = array_filter($snapshots, function ($snapshot) {
            return !(bool) @$snapshot['auto'];
        });
        $ss_auto = array_filter($snapshots, function ($snapshot) {
            return (bool) @$snapshot['auto'];
        });

        //snapshots_size_alert
        $space_usage_total = 0;
        $space_usage_user = 0;
        $space_usage_auto = 0;

        foreach($snapshots as $snapshot){
            if(false === $snapshot['local']){
                continue;
            }
            $space_usage_total += $snapshot['tbl_size'];
            if($snapshot['auto']){
                $space_usage_auto += $snapshot['tbl_size'];
            } else {
                $space_usage_user += $snapshot['tbl_size'];
            }
        }

        if($space_usage_total/1000000 > $options['snapshots_size_alert']){
            echo '<div class="card">';
            echo '<span style="color: #dd3036;">'. $this->get_card_header('Snapshot Database Usage Alert', 'snapshots-user', array('collapse_button' => 0, 'create_snapshot' => false, 'snapshot_actions' => false)) . '</span>';
            echo '<div class="card-body">';
            echo '<p style="color: #dd3036;">';
            echo 'Your snapshots are using ' . WP_Reset_Utility::format_size($space_usage_total) . ' total space in the database!<br />';
            echo 'User snapshots take up ' . WP_Reset_Utility::format_size($space_usage_user) . '<br />';
            echo 'Automatic snapshots take up ' . WP_Reset_Utility::format_size($space_usage_auto) . '<br />';
            echo '</p>';
            echo '<p>If there are snapshots that you don\'t need right away but still want to keep them, you can download them or upload them to cloud and then delete them from your website.
            For automatic snapshots you can also enable the "Automatically delete automatic snapshots" option in the <a class="change-tab" data-tab="4" href="#snapshot-options-group">Settings</a> tab to limit the space used up by automatic snapshots.</p>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="card">';
        echo $this->get_card_header('User Created Snapshots', 'snapshots-user', array('collapse_button' => 1, 'create_snapshot' => true, 'snapshot_actions' => true));
        echo '<div class="card-body">';

        echo '<table id="snapshots-table-user">';
        echo '<tr><th></th><th class="ss-date">Date</th><th>Description</th><th class="ss-size">Size</th><th class="ss-actions">';
        echo '<div class="dropdown">
        <a class="button dropdown-toggle" href="#">Actions</a>
        <div class="dropdown-menu">
        <a title="Import a snapshot" href="#" class="dropdown-item import-snapshot">' . __('Import Snapshot', 'wp-reset') . '</a>
        <a title="Refresh cloud snapshots" href="#" class="dropdown-item refresh-cloud-snapshots">' . __('Refresh Cloud Snapshots', 'wp-reset') . '</a>
        <a title="Delete all user snapshots" data-snapshots="selected_user" href="#" class="dropdown-item delete-snapshots delete-button" data-btn-confirm="Delete selected snapshots" data-text-wait="Deleting snapshots. Please wait." data-text-confirm="Are you sure you want to delete the selected user created snapshots? There is NO UNDO.<br>Deleting the snapshots will not affect the active database tables in any way." data-text-done="%n snapshots deleted." data-text-done-singular="One snapshot deleted.">' . __('Delete Selected User Snapshots', 'wp-reset') . '</a>
        <a title="Delete selected user snapshots" data-snapshots="user" href="#" class="dropdown-item delete-snapshots delete-button" data-btn-confirm="Delete all snapshots" data-text-wait="Deleting snapshots. Please wait." data-text-confirm="Are you sure you want to delete all user created snapshots? There is NO UNDO.<br>Deleting the snapshots will not affect the active database tables in any way." data-text-done="%n snapshots deleted." data-text-done-singular="One snapshot deleted.">' . __('Delete All User Snapshots', 'wp-reset') . '</a>
        </div>
        </div>';
        echo '</th></tr>';
        echo '<tr class="table-empty hidden"><td colspan="5" class="textcenter">There are no user created snapshots. <a href="#" class="create-new-snapshot">Create a new snapshot.</a></td></tr>';
        foreach ((array) $ss_user as $ss) {
            echo $this->get_snapshot_row($ss, 'user');
        } // foreach
        echo '</table>';
        echo '</div>';
        echo '</div>';

        echo '<div class="card">';
        echo $this->get_card_header('Automatic Snapshots', 'snapshots-auto', array('collapse_button' => 1));
        echo '<div class="card-body">';
        echo '<table id="snapshots-table-auto">';
        echo '<tr><th></th><th class="ss-date">Date</th><th>Description</th><th class="ss-size">Size</th><th class="ss-actions">';
        echo '<div class="dropdown">
        <a class="button dropdown-toggle" href="#">Actions</a>
        <div class="dropdown-menu">
        <a title="Delete all auto snapshots" data-snapshots="auto" href="#" class="dropdown-item delete-snapshots delete-button" data-btn-confirm="Delete all snapshots" data-text-wait="Deleting snapshots. Please wait." data-text-confirm="Are you sure you want to delete all automatic snapshots? There is NO UNDO.<br>Deleting the snapshots will not affect the active database tables in any way." data-text-done="%n snapshots deleted." data-text-done-singular="One snapshot deleted.">' . __('Delete All Auto Snapshots', 'wp-reset') . '</a>
        <a title="Delete selected auto snapshots" data-snapshots=selected_auto href="#" class="dropdown-item delete-snapshots delete-button" data-btn-confirm="Delete selected auto snapshots" data-text-wait="Deleting selected automatic snapshots. Please wait." data-text-confirm="Are you sure you want to delete the selected automatic snapshots? There is NO UNDO.<br>Deleting the snapshots will not affect the active database tables in any way." data-text-done="%n snapshots deleted." data-text-done-singular="One snapshot deleted.">' . __('Delete Selected Automatic Snapshots', 'wp-reset') . '</a>
        </div>
        </div>';
        echo '</th></tr>';
        echo '<tr class="table-empty hidden"><td colspan="4" class="textcenter">There are no automatic snapshots.<br>If enabled in <a href="#snapshot-options-group" class="change-tab" data-tab="4">snapshot options</a> they will generate automatically on plugin and theme update, activate, deactivate and similar events.</td></tr>';
        foreach ((array) $ss_auto as $ss) {
            echo $this->get_snapshot_row($ss, 'auto');
        } // foreach
        echo '</table>';
        echo '</div>';
        echo '</div>';
    } // tab_snapshots


    /**
     * Helper function for generating UTM tagged links
     *
     * @param string  $placement  Optional. UTM content param.
     * @param string  $page       Optional. Page to link to.
     * @param array   $params     Optional. Extra URL params.
     * @param string  $anchor     Optional. URL anchor part.
     *
     * @return string
     */
    function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
    {
        $base_url = 'https://wpreset.com';

        if ('/' != $page) {
            $page = '/' . trim($page, '/') . '/';
        }
        if ($page == '//') {
            $page = '/';
        }

        $parts = array_merge(array('utm_source' => 'wp-reset-pro', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-reset-pro-v' . $this->version), $params);

        if (!empty($anchor)) {
            $anchor = '#' . trim($anchor, '#');
        }

        $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

        return $out;
    } // generate_web_link


    /**
     * Helper function for generating dashboard UTM tagged links
     *
     * @param string  $placement  Optional. UTM content param.
     * @param string  $page       Optional. Page to link to.
     * @param array   $params     Optional. Extra URL params.
     * @param string  $anchor     Optional. URL anchor part.
     *
     * @return string
     */
    function generate_dashboard_link($placement = '', $page = '/', $params = array(), $anchor = '')
    {
        $base_url = 'https://dashboard.wpreset.com';

        if ('/' != $page) {
            $page = '/' . trim($page, '/') . '/';
        }
        if ($page == '//') {
            $page = '/';
        }

        $parts = array_merge(array('utm_source' => 'wp-reset-pro', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-reset-pro-v' . $this->version), $params);

        if (!empty($anchor)) {
            $anchor = '#' . trim($anchor, '#');
        }

        $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

        return $out;
    } // generate_dashboard_link


    /**
     * Returns all saved snapshots from DB
     *
     * @return array
     */
    function get_snapshots()
    {
        $snapshots = get_option('wp-reset-snapshots', array());

        if(!is_array($snapshots)){
            $snapshots = array();
        }
        
        foreach ($snapshots as $uid => $snapshot) {
            $snapshots[$uid]['name'] = stripslashes($snapshots[$uid]['name']);
            $snapshots[$uid]['local'] = true;
        }

        return $snapshots;
    } // get_snapshots

    /**
     * Returns all saved snapshots from DB
     *
     * @return array
     */
    function get_all_snapshots()
    {
        global $wp_reset_cloud;

        $snapshots = get_option('wp-reset-snapshots', array());

        foreach ($snapshots as $uid => $snapshot) {
            $snapshots[$uid]['name'] = stripslashes($snapshots[$uid]['name']);
            $snapshots[$uid]['local'] = true;
        }

        $cloud_snapshots = $wp_reset_cloud->get_cloud_snapshots();
        $cloud_only_snapshots = array_diff_key($cloud_snapshots, $snapshots);
        $cloud_common_snapshots = array_intersect_key($snapshots, $cloud_snapshots);

        $snapshots = array_merge($snapshots, $cloud_only_snapshots);

        foreach ($cloud_only_snapshots as $cloud_only_snapshot_uid => $cloud_only_snapshot) {
            $snapshots[$cloud_only_snapshot_uid]['cloud'] = true;
            $snapshots[$cloud_only_snapshot_uid]['local'] = false;
        }

        foreach ($cloud_common_snapshots as $cloud_common_snapshot_uid => $cloud_common_snapshot) {
            $snapshots[$cloud_common_snapshot_uid]['cloud'] = true;
            $snapshots[$cloud_common_snapshot_uid]['cloud_path'] = $cloud_snapshots[$cloud_common_snapshot_uid]['cloud_path'];
            $snapshots[$cloud_common_snapshot_uid]['local'] = true;
        }


        uasort($snapshots, function ($a, $b) {
            return strtotime($a['timestamp']) > strtotime($b['timestamp']);
        });

        return $snapshots;
    } // get_snapshots


    /**
     * Returns all custom table names, with prefix
     *
     * @return array
     */
    function get_custom_tables($only_names = false)
    {
        global $wpdb;
        $custom_tables = array();

        if ($only_names) {
            $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
            foreach ($tables as $table) {
                if (0 !== stripos($table[0], $wpdb->prefix)) {
                    continue;
                }
                if (false === in_array($table[0], $this->core_tables)) {
                    $custom_tables[] = array('name' => $table[0]);
                }
            } // foreach
        } else {
            $table_status = $wpdb->get_results('SHOW TABLE STATUS');
            if (is_array($table_status)) {
                foreach ($table_status as $index => $table) {
                    if (0 !== stripos($table->Name, $wpdb->prefix)) {
                        continue;
                    }
                    if (empty($table->Engine)) {
                        continue;
                    }

                    if (false === in_array($table->Name, $this->core_tables)) {
                        $custom_tables[] = array('name' => $table->Name, 'rows' => $table->Rows, 'data_length' => $table->Data_length, 'index_length' => $table->Index_length);
                    }
                } // foreach
            }
        }

        return $custom_tables;
    } // get_custom tables


    /**
     * Get full path of export file or full directory path without trailing slash, if $file is false
     *
     * @param string $export_file Filename
     * @param bool $url Create URL or file path
     *
     * @return string
     */
    function export_dir_path($file = false, $url = false)
    {
        if ($url) {
            $path = content_url() . '/' . $this->snapshots_folder;
        } else {
            $path = trailingslashit(WP_CONTENT_DIR) . $this->snapshots_folder;
        }

        if ($url === false && !file_exists($path)) {
            $folder = wp_mkdir_p($path);
            if (!$folder) {
                return new WP_Error(1, 'Unable to create ' . $path . ' folder.');
            }
        }

        if (!empty($file)) {
            $path = $path . '/' . $file;
        }

        return $path;
    } // export_dir_path


    /**
     * Get full path of autosnapshot file or just full directory path without trailing slash if $file is false
     *
     * @param string export file
     *
     * @return string
     */
    function autosnapshots_dir_path($file = false, $url = false)
    {
        if ($url) {
            $path = content_url() . '/' . $this->autosnapshots_folder;
        } else {
            $path = trailingslashit(WP_CONTENT_DIR) . $this->autosnapshots_folder;
        }

        if ($url === false && !file_exists($path)) {
            $folder = wp_mkdir_p($path);
            if (!$folder) {
                return new WP_Error(1, 'Unable to create ' . $path . '/ folder.');
            }
        }

        if (!empty($file)) {
            $path = $path . '/' . $file;
        }

        return $path;
    } // autosnapshots_dir_path


    /**
     * Import snapshot
     *
     * @param string  $snapshot absolute path
     *
     * @return bool|WP_Error true on success, or error object on fail.
     */
    function do_import_snapshot($snapshot_zip, $ajax = false)
    {
        global $wpdb;

        $import_zip = new ZipArchive();
        $import_zip->open($snapshot_zip);
        $zip_files = array();

        for ($i = 0; $i < $import_zip->numFiles; $i++) {
            $file_path = $import_zip->statIndex($i);
            $zip_files[] = $file_path['name'];
        }

        if (!in_array('wp-reset-export.json', $zip_files)) {
            return new WP_Error(1, 'Not a valid snapshot export file.');
        }

        $temp_import = $this->export_dir_path('temp_import');
        if (is_wp_error($temp_import)) {
            return $temp_import;
        }

        $import_zip->extractTo($temp_import);
        $import_zip->close();

        $snapshot = json_decode(file_get_contents($temp_import . '/wp-reset-export.json'), true);
        $snapshots = $this->get_snapshots();

        if (!(is_array($snapshot) && array_key_exists('uid', $snapshot))) {
            $this->delete_folder($temp_import, basename($temp_import));
            return new WP_Error(1, 'Snapshot details are invalid or missing.');
        }

        if (array_key_exists($snapshot['uid'], $snapshots)) {
            $this->delete_folder($temp_import, basename($temp_import));
            return new WP_Error(1, 'Uploaded snapshot already exists. Delete the existing one before importing it.');
        }

        if ($snapshot['table_prefix'] != $wpdb->prefix) {
            $this->delete_folder($temp_import, basename($temp_import));
            return new WP_Error(1, 'Table prefix in uploaded snapshot does not match your current table prefix.');
        }

        if ($snapshot['home_url'] != home_url()) {
            $this->delete_folder($temp_import, basename($temp_import));
            return new WP_Error(1, 'Site URL in uploaded snapshot does not match your current site URL.');
        }

        $table_status = $wpdb->get_results('SHOW TABLE STATUS');
        if (is_array($table_status)) {
            foreach ($table_status as $index => $table) {
                if (stripos($table->Name, $snapshot['uid']) === 0) {
                    $this->delete_folder($temp_import, basename($temp_import));
                    return new WP_Error(1, 'Table ' . $table->Name . ' from uploaded snapshot already exist in the current database.');
                }
            }
        }

        if (!empty($snapshot['plugins']) || !empty($snapshot['themes'])) {
            if (!file_exists($temp_import . '/wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip')) {
                $this->delete_folder($temp_import, basename($temp_import));
                return new WP_Error(1, 'Snapshot files are missing.');
            }

            $dest = $this->autosnapshots_dir_path();
            copy($temp_import . '/wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip', $dest . '/wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip');
        }



        $db_dump_file_gz = gzopen($temp_import . '/wp-reset-snapshot-' . $snapshot['uid'] . '.sql.gz', 'rb');
        $db_dump_file_sql = fopen($temp_import . '/wp-reset-snapshot-' . $snapshot['uid'] . '.sql', 'wb+');
        while (!gzeof($db_dump_file_gz)) {
            fwrite($db_dump_file_sql, gzread($db_dump_file_gz, 4096));
        }
        rewind($db_dump_file_sql);
        gzclose($db_dump_file_gz);

        $wprdb = false;

        //Skip Queries if we're doing ajax, just return number of lines to generate steps

        if (!$ajax) {
            $db_info = $this->get_db_info();
            $wprdb = mysqli_connect($db_info['host'], DB_USER, DB_PASSWORD, DB_NAME, $db_info['port']);
        }

        $line_ending = PHP_EOL;

        $parse_result = $this->parse_sql_dump($wprdb, $db_dump_file_sql, $line_ending, $ajax);

        if ($parse_result['lines'] == 0) {
            $line_ending = "\n";
            $parse_result = $this->parse_sql_dump($wprdb, $db_dump_file_sql, $line_ending, $ajax);
        }
        if ($parse_result['lines'] == 0) {
            $line_ending = "\r\n";
            $parse_result = $this->parse_sql_dump($wprdb, $db_dump_file_sql, $line_ending, $ajax);
        }

        $parse_result['line_ending'] = $line_ending;
        $parse_result['uid'] = $snapshot['uid'];

        if (!$ajax) {
            $wprdb->close();
        }
        fclose($db_dump_file_sql);

        // If doing ajax we just return steps
        if ($ajax) {
            $snapshot['partial'] = true;
            $snapshots[$snapshot['uid']] = $snapshot;
            update_option('wp-reset-snapshots', $snapshots);

            $increment = 10;
            $current_table = 'setup queries';

            for ($line = 0; $line < $parse_result['lines']; $line += $increment) {
                foreach ($parse_result['tables'] as $table => $sql_line) {
                    if ($line > $sql_line) {
                        $current_table = $table;
                        break;
                    }
                }
                $steps[] = array('uid' => $snapshot['uid'], 'action' => 'import', 'start' => $line, 'end' => ($line + $increment), 'line_ending' => $line_ending, 'description' => 'Importing lines <i>' . $line . ' - ' . ($line + $increment) . ' / ' . $current_table . ')</i>');
            }
            $steps[] = array('uid' => $snapshot['uid'], 'action' => 'import_verify', 'description' => 'Verifying imported snapshot</i>');

            return $steps;
        }

        return $this->do_import_snapshot_wrap($snapshot);
    } // do_import_snapshot

    /**
     * Finish snapshot import
     *
     * @param array  $snapshot
     *
     * @return bool|WP_Error true on success, or error object on fail.
     */
    function do_import_snapshot_wrap($snapshot)
    {
        global $wpdb;

        $snapshots = $this->get_snapshots();

        $temp_import = $this->export_dir_path('temp_import');
        if (is_wp_error($temp_import)) {
            return $temp_import;
        }

        $tbl_core = $tbl_custom = 0;
        $tables = $wpdb->get_col("SHOW TABLES LIKE '" . $snapshot['uid'] . "%'");
        $imported_tables = array();
        foreach ($tables as $table) {
            $imported_tables[] = $table;
            if (in_array(str_ireplace($snapshot['uid'] . '_', '', $table), $this->core_tables)) {
                $tbl_core++;
            } else {
                $tbl_custom++;
            }
        }

        if ($snapshot['tbl_core'] != $tbl_core || $snapshot['tbl_custom'] != $tbl_custom) {
            $this->delete_folder($temp_import, basename($temp_import));
            foreach ($imported_tables as $table) {
                $wpdb->query('DROP TABLE IF EXISTS `' . $table . '`');
            }
            return new WP_Error(1, 'Imported tables do not match expected table count. ' . $snapshot['tbl_core'] . '/' . $tbl_core . '|' . $snapshot['tbl_custom'] . '/' . $tbl_custom);
        }

        $this->delete_folder($temp_import, basename($temp_import));

        $snapshot['imported'] = current_time('mysql');
        $snapshot['partial'] = false;
        $snapshots[$snapshot['uid']] = $snapshot;
        update_option('wp-reset-snapshots', $snapshots);
        do_action('wp_reset_import_snapshot', $snapshot['uid'], $snapshot);

        return true;
    }

    /**
     * Do snapshot import step
     *
     * @param array  $step_data
     *
     * @return bool|WP_Error true on success, or error object on fail.
     */
    function import_snapshot_step($step_data)
    {
        $options = $this->get_options();
        switch ($step_data['action']) {
            case 'import':
                $db_info = $this->get_db_info();
                $wprdb = mysqli_connect($db_info['host'], DB_USER, DB_PASSWORD, DB_NAME, $db_info['port']);

                $temp_import = $this->export_dir_path('temp_import');
                $db_dump_file_sql = fopen($temp_import . '/wp-reset-snapshot-' . $step_data['uid'] . '.sql', 'r');

                $current_line = 0;

                $wprdb->query('/*!40030 SET NAMES UTF8 */;
            /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
            /*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
            /*!40103 SET TIME_ZONE=\'+00:00\' */;
            /*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
            /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
            /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;
            /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;');

                while (!feof($db_dump_file_sql)) {
                    $buffer = trim(stream_get_line($db_dump_file_sql, 100000000, ";" . $step_data['line_ending']));

                    if (substr($buffer, 0, 2) == '--' || trim($buffer) == '') {
                        continue;
                    }

                    if (strpos(trim($buffer), '/*!') === 0) {
                        $current_line++;
                        continue;
                    }

                    if ($current_line >= $step_data['start'] && $current_line < $step_data['end']) {
                        $buffer = preg_replace('/[\x09]/', '\t', trim($buffer));
                        if ($options['fix_datetime'] == true) {
                            $buffer = str_replace("datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", "datetime DEFAULT NULL", $buffer);
                            $buffer = str_replace("timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'", "timestamp DEFAULT NULL", $buffer);
                            $buffer = str_replace("'0000-00-00 00:00:00'", "NULL", $buffer);
                        }
                        if (!$wprdb->query($buffer)) {
                            return new WP_Error(1, 'SQL error: ' . $wprdb->error);
                        }
                    } else if ($current_line > $step_data['end']) {
                        break;
                    }

                    $current_line++;
                }

                $wprdb->query('/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
            /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
            /*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
            /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
            /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
            /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
            /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;');
                break;
            case 'import_verify':
                $snapshots = $this->get_snapshots();
                return $this->do_import_snapshot_wrap($snapshots[$step_data['uid']]);
                break;
        }

        return true;
    }

    /**
     * Parse SQL dump file using a specified $line_ending
     *
     * @param object $wprdb database handle
     * @param object $db_dump_file_sql SQL file handle
     * @param string $line_ending
     * @param bool   $ajax set to true if called via ajax
     *
     * @return bool|WP_Error true on success, or error object on fail.
     */
    function parse_sql_dump($wprdb, $db_dump_file_sql, $line_ending = PHP_EOL, $ajax)
    {
        $options = $this->get_options();
        $tables = array();
        $lines_read = 0;
        rewind($db_dump_file_sql);
        while (!feof($db_dump_file_sql)) {
            $buffer = stream_get_line($db_dump_file_sql, 100000000, ";" . $line_ending);

            if (substr($buffer, 0, 2) == '--' || trim($buffer) == '') {
                continue;
            }

            $lines_read++;
            if (!empty($buffer)) {
                if (!$ajax) {
                    $buffer = preg_replace('/[\x09]/', '\t', trim($buffer));
                    if ($options['fix_datetime'] == true) {
                        $buffer = str_replace("datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", "datetime DEFAULT NULL", $buffer);
                        $buffer = str_replace("timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'", "timestamp DEFAULT NULL", $buffer);
                        $buffer = str_replace("'0000-00-00 00:00:00'", "NULL", $buffer);
                    }
                    $wprdb->query($buffer);
                } else {
                    if (strpos($buffer, 'CREATE TABLE `') === 0) {
                        $query_parts = explode('`', $buffer);
                        $tables[$query_parts[1]] = $lines_read;
                    }
                }
            }
        }

        return array('lines' => $lines_read, 'tables' => $tables);
    }

    /**
     * Creates snapshot of current tables by copying them in the DB and saving metadata; plugin/theme files are included if specified.
     *
     * @param array  $snapshot  Optional snapshot meta and options
     *
     * @return array|WP_Error Snapshot details in array on success, or error object on fail.
     */
    function do_create_snapshot($params = array())
    {
        global $wpdb;
        $options = $this->get_options();
        $snapshots = $this->get_snapshots();
        $snapshot = shortcode_atts(array('name' => '', 'plugins' => array(), 'themes' => array(), 'auto' => false, 'autoupload' => false, 'ajax' => false, 'imported' => false), (array) $params);
        $uid = $this->generate_snapshot_uid();
        $tbl_core = $tbl_custom = $tbl_size = $tbl_rows = 0;

        if (!$uid) {
            return new WP_Error(1, 'Unable to generate a valid snapshot UID.');
        }

        if (!class_exists('ZipArchive')) {
            return new WP_Error(1, 'The PHP ZipArchive Library is missing or disabled! Please contact your host to enable it in order to create snapshots.');
        }

        if ($snapshot['name']) {
            $snapshot['name'] = substr(trim($snapshot['name']), 0, 512);
        }

        $snapshot['uid'] = $uid;
        $snapshot['timestamp'] = current_time('mysql');
        $snapshot['tbl_names'] = array();

        if ($snapshot['ajax']) {
            $snapshot['partial'] = true;
        } else if ($options['autosnapshots_autoupload'] == true && $snapshot['auto'] == true && !empty($options['cloud_service']) && array_key_exists($options['cloud_service'], $this->cloud_services)) {
            $snapshot['autoupload'] = true;
        }

        $zip_folders = array();

        if (!empty($snapshot['plugins']) && is_array($snapshot['plugins'])) {
            foreach ($snapshot['plugins'] as $plugin => $path) {
                if (file_exists($path)) {
                    $zip_folders[] = $path;
                } else {
                    unset($snapshot['plugins'][$plugin]);
                }
            }
        }

        if (!empty($snapshot['themes']) && is_array($snapshot['themes'])) {
            foreach ($snapshot['themes'] as $theme => $path) {
                if (file_exists($path)) {
                    $zip_folders[] = $path;
                } else {
                    unset($snapshot['themes'][$theme]);
                }
            }
        }

        if (count($zip_folders)) {
            $zip_filename = $this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $uid . '.zip');
            $zip = new ZipArchive();
            $zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($zip_folders as $zip_folder) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($zip_folder),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = basename($zip_folder) . '/' . substr($filePath, strlen($zip_folder));
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }
            $zip->close();
            $snapshot['file_size']  = filesize($zip_filename);
        }

        $table_status = $wpdb->get_results('SHOW TABLE STATUS');
        if (is_array($table_status)) {
            foreach ($table_status as $index => $table) {
                if (0 !== stripos($table->Name, $wpdb->prefix)) {
                    continue;
                }
                if (empty($table->Engine)) {
                    continue;
                }

                $tbl_rows += $table->Rows;
                $tbl_size += $table->Data_length + $table->Index_length;
                if (in_array($table->Name, $this->core_tables)) {
                    $tbl_core++;
                } else {
                    $tbl_custom++;
                }

                $snapshot['tbl_names'][] = $table->Name;

                if (!$snapshot['ajax']) {
                    if ($options['optimize_tables'] == true) {
                        $wpdb->query('OPTIMIZE TABLE `' . $table->Name . '`');
                    }
                    $wpdb->query('CREATE TABLE `' . $uid . '_' . $table->Name . '` LIKE `' . $table->Name . '`');
                    $wpdb->query('INSERT `' . $uid . '_' . $table->Name . '` SELECT * FROM `' . $table->Name . '`');
                }
            } // foreach
        } else {
            return new WP_Error(1, 'Can\'t get table status data.');
        }

        $snapshot['tbl_core']   = $tbl_core;
        $snapshot['tbl_custom'] = $tbl_custom;
        $snapshot['tbl_rows']   = $tbl_rows;
        $snapshot['tbl_size']   = $tbl_size;

        $snapshots[$uid] = $snapshot;
        update_option('wp-reset-snapshots', $snapshots);
        do_action('wp_reset_create_snapshot', $uid, $snapshot);

        return $snapshot;
    } // create_snapshot

    /**
     * Process a snapshot step
     *
     * @param array  $step_data containing snapshot uid, action and extra data
     *
     * @return bool|WP_Error true on success, WP_Error on error
     */
    function create_snapshot_step($step_data)
    {
        global $wpdb, $wp_reset_cloud;

        $options = $this->get_options();

        if (!isset($step_data['uid'])) {
            return new WP_Error(1, 'Invalid snapshot ID');
        }

        $snapshots = $this->get_snapshots();

        if (!array_key_exists($step_data['uid'], $snapshots)) {
            return new WP_Error(1, 'Unknown snapshot ID');
        }

        $snapshot = $snapshots[$step_data['uid']];

        if (!isset($snapshot['partial']) || $snapshot['partial'] != true) {
            return new WP_Error(1, 'Snapshot already created');
        }

        switch ($step_data['action']) {
            case 'copy':
                $wpdb->show_errors = false;
                $wpdb->suppress_errors = false;

                if ($options['optimize_tables'] == true) {
                    $wpdb->query('OPTIMIZE TABLE ' . $step_data['data']);
                }

                $wpdb->query('CREATE TABLE `' . $step_data['uid'] . '_' . $step_data['data'] . '` LIKE `' . $step_data['data'] . '`');
                $wpdb->query('INSERT `' . $step_data['uid'] . '_' . $step_data['data'] . '` SELECT * FROM `' . $step_data['data'] . '`');
                if ($wpdb->last_error !== '') {
                    return new WP_Error(1, 'An database error occurred: ' . $wpdb->last_error);
                }
                break;
            case 'verify_integrity':
                $verify_snapshot = $this->verify_snapshot_integrity($step_data['uid']);
                if ($verify_snapshot === true) {
                    $snapshot['partial'] = false;
                    if ($options['autosnapshots_autoupload'] == true && $snapshot['auto'] == true && !empty($options['cloud_service']) && array_key_exists($options['cloud_service'], $this->cloud_services)) {
                        $snapshot['autoupload'] = true;
                    }
                    $snapshots[$step_data['uid']] = $snapshot;
                    update_option('wp-reset-snapshots', $snapshots);
                } else {
                    $this->do_delete_snapshot($step_data['uid']);
                    return $verify_snapshot;
                }
                break;
            default:
                return new WP_Error(1, 'Unknown snapshot step action');
                break;
        }

        return $snapshot;
    } // create_snapshot_step

    /**
     * Delete snapshot metadata and tables from DB
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function do_delete_snapshot($uid = '')
    {
        global $wpdb;
        $snapshots = $this->get_snapshots();
        $this->log('info', 'Deleting snapshot ' . $uid);
        if (strlen($uid) != 4 && strlen($uid) != 6) {
            return new WP_Error(1, 'Invalid UID format.');
        }

        if (!isset($snapshots[$uid])) {
            return new WP_Error(1, 'Unknown snapshot ID.');
        }

        $tables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', array($uid . '\_%')));
        foreach ($tables as $table) {
            $wpdb->query('DROP TABLE IF EXISTS `' . $table . '`');
            if ($wpdb->last_error !== '') {
                $this->log('error', 'Failed to delete table ' . $table . ': ' . $wpdb->last_error);
            } else {
                $this->log('info', 'Deleted table ' . $table);
            }
        }

        @unlink($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $uid . '.zip'));
        @unlink($this->export_dir_path('wp-reset-snapshot-' . $uid . '.zip'));

        $snapshot_copy = $snapshots[$uid];
        unset($snapshots[$uid]);
        update_option('wp-reset-snapshots', $snapshots);

        do_action('wp_reset_delete_snapshot', $uid, $snapshot_copy);
        $this->log('info', 'Deleted snapshot ' . $uid . ' successfully');
        
        return true;
    } // delete_snapshot


    /**
     * Delete snapshot tables from DB by snapshot UID
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function delete_snapshot_tables($uid = ''){
        global $wpdb;

        if(empty($uid)){
            $uid = $_GET['uid'];
        }

        if (strlen($uid) != 4 && strlen($uid) != 6) {
            return new WP_Error(1, 'Invalid UID format.');
        }

        $tables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', array($uid . '\_%')));
        foreach ($tables as $table) {
            $wpdb->query('DROP TABLE IF EXISTS `' . $table . '`');
            if ($wpdb->last_error !== '') {
                $this->log('error', 'Failed to delete table ' . $table . ': ' . $wpdb->last_error);
            } else {
                $this->log('info', 'Deleted table ' . $table);
            }
        }

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        }

        return true;
    }

    /**
     * Delete temporary files from wp-reset-snapshots-export
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function delete_temporary_files(){
        if(file_exists($this->export_dir_path())){
            $this->delete_folder($this->export_dir_path(), basename($this->export_dir_path()));
        }
        
        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        }

        return true;
    }
    

    /**
     * Delete all snapshots
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function do_delete_snapshots($params = array())
    {
        $snapshots = $this->get_snapshots();
        $deleted_snapshots = 0;
        
        if($params['delete'] == 'selected'){
            foreach($params['ids'] as $uid){
                if(array_key_exists($uid, $snapshots)){
                    $delete_result = $this->do_delete_snapshot($uid);
                    if (is_wp_error($delete_result)) {
                        return $delete_result;
                    } else {
                        $deleted_snapshots++;
                    }
                }
            }
        } else {
            foreach ($snapshots as $uid => $snapshot) {
                if (($params['delete'] == 'auto' && $snapshot['auto'] == true) || 
                    ($params['delete'] == 'user' && (
                        !array_key_exists('auto', $snapshot) || 
                        $snapshot['auto'] == false)
                    )
                ) {
                    $delete_result = $this->do_delete_snapshot($uid);
                    if (is_wp_error($delete_result)) {
                        return $delete_result;
                    } else {
                        $deleted_snapshots++;
                    }
                }
            }
        }

        return $deleted_snapshots;
    } // do_delete_snapshots


    /**
     * Exports snapshot as SQL dump; saved in gzipped file in WP_CONTENT folder.
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return string|WP_Error Export base filename, or error object on fail.
     */
    function do_export_snapshot($uid = '', $background = false)
    {
        global $wpdb;

        $snapshots = $this->get_snapshots();
        $options = $this->get_all_options();
        $ajax = false;

        if (!class_exists('ZipArchive')) {
            return new WP_Error(1, 'The PHP ZipArchive Library is missing or disabled! Please contact your host to enable it in order to export snapshots.');
        }

        if ($options['options']['ajax_snapshots_export'] == 1 || $background) {
            $ajax = true;
        }

        if (strlen($uid) != 4 && strlen($uid) != 6) {
            return new WP_Error(1, 'Invalid snapshot ID format.');
        }

        if (!isset($snapshots[$uid])) {
            return new WP_Error(1, 'Unknown snapshot ID.');
        }

        $snapshot = $snapshots[$uid];
        $snapshot['ajax'] = $ajax;
        $snapshot['tbl_names'] = array();

        $htaccess_content = 'AddType application/octet-stream .gz' . PHP_EOL;
        $htaccess_content .= 'Options -Indexes' . PHP_EOL;
        $htaccess_file = @fopen($this->export_dir_path('.htaccess'), 'w');
        if ($htaccess_file) {
            fputs($htaccess_file, $htaccess_content);
            fclose($htaccess_file);
        }

        if ($ajax) {

            $dump_file_path = $this->export_dir_path('wp-reset-snapshot-' . $uid . '.sql');

            if (is_wp_error($dump_file_path)) {
                return $dump_file_path;
            }

            if (file_exists($dump_file_path)) {
                unlink($dump_file_path);
            }

            $table_status = $wpdb->get_results('SHOW TABLE STATUS');

            if (is_array($table_status)) {
                foreach ($table_status as $index => $table) {
                    if (0 !== stripos($table->Name, $uid . '_' . $wpdb->prefix)) {
                        continue;
                    }
                    if (empty($table->Engine)) {
                        continue;
                    }

                    $snapshot['tbl_names'][] = $table->Name;
                } // foreach

                return $snapshot;
            } else {
                return new WP_Error(1, 'Can\'t get table status data.');
            }
        } else {
            require_once $this->plugin_dir . 'libs/dumper.php';

            $dump_file_path = $this->export_dir_path('wp-reset-snapshot-' . $uid . '.sql.gz');
            if (is_wp_error($dump_file_path)) {
                return $dump_file_path;
            }



            try {
                $db_info = $this->get_db_info();

                $world_dumper = WPR_Shuttle_Dumper::create(array(
                    'host' => $db_info['host'],
                    'port' => $db_info['port'],
                    'username' => DB_USER,
                    'password' => DB_PASSWORD,
                    'db_name' =>  DB_NAME,
                ));

                $world_dumper->dump($dump_file_path, $uid . '_');
            } catch (WPR_Shuttle_Exception $e) {
                return new WP_Error(1, 'Couldn\'t create snapshot: ' . $e->getMessage());
            }

            if (!(empty($snapshot['plugins']) && empty($snapshot['themes'])) && !file_exists($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $uid . '.zip'))) {
                return new WP_Error(1, 'Snapshot files missing: ' . $this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $uid . '.zip'));
            }
        }

        $this->do_export_snapshot_wrap($uid);

        return $this->export_dir_path('wp-reset-snapshot-' . $uid . '.zip', true);
    } // do_export_snapshot

    /**
     * Process a snapshot step
     *
     * @param array  $step_data containing snapshot uid, action and extra data
     *
     * @return bool|WP_Error true on success, WP_Error on error
     */
    function export_snapshot_step($step_data)
    {
        global $wpdb;

        $dump_file_path = $this->export_dir_path('wp-reset-snapshot-' . $step_data['uid'] . '.sql');
        if (is_wp_error($dump_file_path)) {
            return $dump_file_path;
        }

        switch ($step_data['action']) {
            case 'export':
                require_once $this->plugin_dir . 'libs/dumper.php';
                try {
                    $db_info = $this->get_db_info();
                    clearstatcache();

                    $world_dumper = WPR_Shuttle_Dumper::create(array(
                        'host' => $db_info['host'],
                        'port' => $db_info['port'],
                        'username' => DB_USER,
                        'password' => DB_PASSWORD,
                        'db_name' =>  DB_NAME,
                    ));

                    $world_dumper->dump($dump_file_path, $step_data['data']);
                } catch (WPR_Shuttle_Exception $e) {
                    return new WP_Error(1, 'Couldn\'t create snapshot: ' . $e->getMessage());
                }
                break;
            case 'verify_export':
                WP_Reset_Utility::gzCompressFile($dump_file_path);
                unlink($dump_file_path);
                $this->do_export_snapshot_wrap($step_data['uid']);

                if ($this->verify_snapshot_integrity($step_data['uid']) === true) {
                    $snapshots = $this->get_snapshots();
                    $snapshots[$step_data['uid']]['partial'] = false;
                    update_option('wp-reset-snapshots', $snapshots);
                } else {
                    $this->do_delete_snapshot($step_data['uid']);
                    return new WP_Error(1, 'Snapshot verification failed and it has been deleted. Contact WP Reset support if this error repeats.');
                }
                return $this->export_dir_path('wp-reset-snapshot-' . $step_data['uid'] . '.zip', true);
                break;
            default:
                return new WP_Error(1, 'Unknown export snapshot step action');
                break;
        }

        return true;
    } // export_snapshot_step

    function do_export_snapshot_wrap($uid)
    {
        global $wpdb;

        $snapshots = $this->get_snapshots();

        $snapshot_json_file_path = $this->export_dir_path('wp-reset-export.json');
        if (is_wp_error($snapshot_json_file_path)) {
            return $snapshot_json_file_path;
        }

        $snapshot_json = @fopen($snapshot_json_file_path, 'w');
        $snapshots[$uid]['export_timestamp'] = current_time('mysql');
        $snapshots[$uid]['wpr_version'] = $this->version;
        $snapshots[$uid]['table_prefix'] = $wpdb->prefix;
        $snapshots[$uid]['home_url'] = home_url();
        fputs($snapshot_json, json_encode($snapshots[$uid]));
        fclose($snapshot_json);

        $export_zip_filename = $this->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');
        if (is_wp_error($export_zip_filename)) {
            return $export_zip_filename;
        }

        $zip = new ZipArchive();
        if (file_exists($export_zip_filename)) {
            unlink($export_zip_filename);
        }
        $zip->open($export_zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($this->export_dir_path('wp-reset-export.json'), 'wp-reset-export.json');
        $zip->addFile($this->export_dir_path('wp-reset-snapshot-' . $uid . '.sql.gz'), 'wp-reset-snapshot-' . $uid . '.sql.gz');

        if (!(empty($snapshots[$uid]['plugins']) && empty($snapshots[$uid]['themes']))) {
            $zip->addFile($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $uid . '.zip'), 'wp-reset-snapshot-files-' . $uid . '.zip');
        }
        $zip->close();

        @unlink($this->export_dir_path('wp-reset-export.json'));
        @unlink($this->export_dir_path('wp-reset-snapshot-' . $uid . '.sql.gz'));

        do_action('wp_reset_export_snapshot', 'wp-reset-snapshot-' . $uid . '.zip', $uid, $snapshots[$uid]);
    } // do_export_snapshot_wrap


    /**
     * Replace current tables with ones in snapshot, unzip attached files if available
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function do_restore_snapshot($uid = '')
    {
        global $wpdb, $wp_reset_cloud;
        $user_id = get_current_user_id();
        $new_tables = array();
        $snapshots = $this->get_snapshots();
        $wf_licensing_wpr = get_option('wf_licensing_wpr');
        $cloud_snapshots = $wp_reset_cloud->get_cloud_snapshots();

        if (($res = $this->verify_snapshot_integrity($uid)) !== true) {
            return $res;
        }

        if (!empty($snapshots[$uid]['plugins']) || !empty($snapshots[$uid]['themes'])) {
            if (($res = $this->restore_snapshot_files($snapshots[$uid])) !== true) {
                return $res;
            }
        }

        $table_status = $wpdb->get_results('SHOW TABLE STATUS');
        if (is_array($table_status)) {
            foreach ($table_status as $index => $table) {
                if (0 !== stripos($table->Name, $uid . '_')) {
                    continue;
                }
                if (empty($table->Engine)) {
                    continue;
                }

                $new_tables[] = $table->Name;
            } // foreach
        } else {
            return new WP_Error(1, 'Can\'t get table status data.');
        }

        foreach ($table_status as $index => $table) {
            if (0 !== stripos($table->Name, $wpdb->prefix)) {
                continue;
            }
            if (empty($table->Engine)) {
                continue;
            }

            $wpdb->query('DROP TABLE `' . $table->Name . '`');
        } // foreach

        // copy snapshot tables to original name
        foreach ($new_tables as $table) {
            $new_name = str_replace($uid . '_', '', $table);

            $wpdb->query('CREATE TABLE `' . $new_name . '` LIKE `' . $table . '`');
            $wpdb->query('INSERT `' . $new_name . '` SELECT * FROM `' . $table . '`');
        }

        wp_cache_flush();
        update_option('wp-reset', $this->options);
        update_option('wp-reset-snapshots', $snapshots);
        update_option('wp-reset-cloud-snapshots', $cloud_snapshots);
        update_option('wf_licensing_wpr', $wf_licensing_wpr);


        if (!$this->is_cli_running()) {
            wp_clear_auth_cookie();
            wp_set_auth_cookie($user_id);
        }

        do_action('wp_reset_restore_snapshot', $uid);

        return true;
    } // restore_snapshot


    /**
     * Verifies snapshot zip integrity by comparing metadata folders and size
     *
     * @param string  @param array  $snapshot  Snapshot details
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function verify_zip_integrity($snapshot)
    {
        if (!array_key_exists('file_size',$snapshot) || $snapshot['file_size'] != filesize($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip'))) {
            return new WP_Error(1, 'ZIP file size is not correct!');
        }

        $folders = array();
        if (!empty($snapshot['plugins'])) {
            foreach ($snapshot['plugins'] as $plugin) {
                $folders[] = basename($plugin);
            }
        }

        if (!empty($snapshot['themes'])) {
            foreach ($snapshot['themes'] as $theme) {
                $folders[] = basename($theme);
            }
        }
        sort($folders);

        $zip_folders = array();
        $za = new ZipArchive();
        $za->open($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip'));
        for ($i = 0; $i < $za->numFiles; $i++) {
            $file_path = $za->statIndex($i);
            $file_path_parts = explode('/', $file_path['name']);
            if (!in_array($file_path_parts[0], $zip_folders)) {
                $zip_folders[] = $file_path_parts[0];
            }
        }
        sort($zip_folders);

        if ($folders === $zip_folders) {
            return true;
        } else {
            return new WP_Error(1, 'Snapshot ZIP files have failed integrity validation!');
        }
    } // verify_zip_integrity


    /**
     * Restore snapshot files
     *
     * @param array  $snapshot  Snapshot details
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function restore_snapshot_files($snapshot)
    {
        $temp_dir = $this->autosnapshots_dir_path('_tmp_wp-reset-snapshot-files-' . $snapshot['uid']);

        $archive_files = new ZipArchive;
        $archive_files->open($this->autosnapshots_dir_path('wp-reset-snapshot-files-' . $snapshot['uid'] . '.zip'));
        $archive_files->extractTo($temp_dir);

        if (!empty($snapshot['plugins'])) {
            foreach ($snapshot['plugins'] as $plugin) {
                $folder = basename($plugin);
                $this->delete_folder(WP_PLUGIN_DIR . '/' . $folder, WP_PLUGIN_DIR . '/' . $folder);
                $this->copy_folder($temp_dir . '/' . $folder, WP_PLUGIN_DIR . '/' . $folder);
            }
        }

        if (!empty($snapshot['themes'])) {
            foreach ($snapshot['themes'] as $theme) {
                $folder = basename($theme);
                $this->delete_folder(get_theme_root() . '/' . $folder, get_theme_root() . '/' . $folder);
                $this->copy_folder($temp_dir . '/' . $folder, get_theme_root() . '/' . $folder);
            }
        }

        $archive_files->close();
        $this->delete_folder($temp_dir, $this->autosnapshots_dir_path());

        return true;
    } // restore_snapshot_files


    /**
     * Verifies snapshot integrity by comparing metadata and data in DB
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return bool|WP_Error True on success, or error object on fail.
     */
    function verify_snapshot_integrity($uid)
    {
        global $wpdb;
        $tbl_core = $tbl_custom = 0;
        $snapshots = $this->get_snapshots();

        if (strlen($uid) != 4 && strlen($uid) != 6) {
            return new WP_Error(1, 'Invalid snapshot ID format.');
        }

        if (!isset($snapshots[$uid])) {
            return new WP_Error(1, 'Unknown snapshot ID.');
        }

        $snapshot = $snapshots[$uid];

        if (!empty($snapshot['plugins']) || !empty($snapshot['themes'])) {
            $res = $this->verify_zip_integrity($snapshot);
            if ($res !== true) {
                return $res;
            }
        }

        $table_status = $wpdb->get_results('SHOW TABLE STATUS');
        if (is_array($table_status)) {
            foreach ($table_status as $table) {
                if (0 !== stripos($table->Name, $uid . '_')) {
                    continue;
                }
                if (empty($table->Engine)) {
                    continue;
                }

                if (in_array(str_replace($uid . '_', '', $table->Name), $this->core_tables)) {
                    $tbl_core++;
                } else {
                    $tbl_custom++;
                }
            } // foreach

            if ($tbl_core != $snapshot['tbl_core'] || $tbl_custom != $snapshot['tbl_custom']) {
                return new WP_Error(1, 'Snapshot data has been compromised. Saved metadata does not match data in the DB (' . $tbl_core . ' / ' . $snapshot['tbl_core'] . ' core, ' . $tbl_custom . ' / ' . $snapshot['tbl_custom'] . ' core). Contact WP Reset support if data is critical, or restore it manualy via SQL.');
            }
        } else {
            return new WP_Error(1, 'Can\'t get table status data.');
        }

        return true;
    } // verify_snapshot_integrity


    /**
     * Compares a selected snapshot with the current table set in DB
     *
     * @param string  $uid  Snapshot unique 6-char ID.
     *
     * @return string|WP_Error Formatted table with details on success, or error object on fail.
     */
    function do_compare_snapshots($uid)
    {
        global $wpdb;
        $current = $snapshot = array();
        $out = $out2 = $out3 = '';

        if (($res = $this->verify_snapshot_integrity($uid)) !== true) {
            return $res;
        }

        $table_status = $wpdb->get_results('SHOW TABLE STATUS');
        foreach ($table_status as $index => $table) {
            if (empty($table->Engine)) {
                continue;
            }

            if (0 !== stripos($table->Name, $uid . '_') && 0 !== stripos($table->Name, $wpdb->prefix)) {
                continue;
            }

            $info = array();
            $info['rows'] = $table->Rows;
            $info['size_data'] = $table->Data_length;
            $info['size_index'] = $table->Index_length;
            $schema = $wpdb->get_row('SHOW CREATE TABLE ' . $table->Name, ARRAY_N);
            $info['schema'] = $schema[1];
            $info['engine'] = $table->Engine;
            $info['fullname'] = $table->Name;
            $basename = str_replace(array($uid . '_'), array(''), $table->Name);
            $info['basename'] = $basename;
            $info['corename'] = str_replace(array($wpdb->prefix), array(''), $basename);
            $info['uid'] = $uid;

            if (0 === stripos($table->Name, $uid . '_')) {
                $snapshot[$basename] = $info;
            }

            if (0 === stripos($table->Name, $wpdb->prefix)) {
                $info['uid'] = '';
                $current[$basename] = $info;
            }
        } // foreach

        $in_both = array_keys(array_intersect_key($current, $snapshot));
        $in_current_only = array_diff_key($current, $snapshot);
        $in_snapshot_only = array_diff_key($snapshot, $current);

        $out .= '<br><br>';
        foreach ($in_current_only as $table) {
            $out .= '<div class="wpr-table-container in-current-only" data-table="' . $table['basename'] . '">';
            $out .= '<table>';
            $out .= '<tr title="Click to show/hide more info" class="wpr-table-missing header-row">';
            $out .= '<td><b>' . $table['fullname'] . '</b></td>';
            $out .= '<td>table is not present in snapshot<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
            $out .= '</tr>';
            $out .= '<tr class="hidden">';
            $out .= '<td>';
            $out .= '<p>' . number_format($table['rows']) . ' row' . ($table['rows'] == 1 ? '' : 's') . ' totaling ' . WP_Reset_Utility::format_size($table['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($table['size_index']) . ' in index.</p>';
            $out .= '<pre>' . $table['schema'] . '</pre>';
            $out .= '</td>';
            $out .= '<td>&nbsp;</td>';
            $out .= '</tr>';
            $out .= '</table>';
            $out .= '</div>';
        } // foreach in current only

        foreach ($in_snapshot_only as $table) {
            $out .= '<div class="wpr-table-container in-snapshot-only" data-table="' . $table['basename'] . '">';
            $out .= '<table>';
            $out .= '<tr title="Click to show/hide more info" class="wpr-table-missing header-row">';
            $out .= '<td>table is not present in current tables</td>';
            $out .= '<td><b>' . $table['fullname'] . '</b><span class="dashicons dashicons-arrow-down-alt2"></span></td>';
            $out .= '</tr>';
            $out .= '<tr class="hidden">';
            $out .= '<td>&nbsp;</td>';
            $out .= '<td>';
            $out .= '<p>' . number_format($table['rows']) . ' row' . ($table['rows'] == 1 ? '' : 's') . ' totaling ' . WP_Reset_Utility::format_size($table['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($table['size_index']) . ' in index.</p>';
            $out .= '<pre>' . $table['schema'] . '</pre>';
            $out .= '</td>';
            $out .= '</tr>';
            $out .= '</table>';
            $out .= '</div>';
        } // foreach in snapshot only

        foreach ($in_both as $tablename) {
            $tbl_current = $current[$tablename];
            $tbl_snapshot = $snapshot[$tablename];

            $schema1 = preg_replace('/(auto_increment=)([0-9]*) /i', '${1}1 ', $tbl_current['schema'], 1);
            $schema2 = preg_replace('/(auto_increment=)([0-9]*) /i', '${1}1 ', $tbl_snapshot['schema'], 1);
            $tbl_snapshot['tmp_schema'] = str_replace($tbl_snapshot['uid'] . '_' . $tablename, $tablename, $tbl_snapshot['schema']);
            $schema2 = str_replace($tbl_snapshot['uid'] . '_' . $tablename, $tablename, $schema2);

            if ($tbl_current['rows'] == $tbl_snapshot['rows'] && $tbl_current['schema'] == $tbl_snapshot['tmp_schema']) {
                $out3 .= '<div class="wpr-table-container identical" data-table="' . $tablename . '">';
                $out3 .= '<table>';
                $out3 .= '<tr title="Click to show/hide more info" class="wpr-table-match header-row">';
                $out3 .= '<td><b>' . $tbl_current['fullname'] . '</b></td>';
                $out3 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b><span class="dashicons dashicons-arrow-down-alt2"></span></td>';
                $out3 .= '</tr>';
                $out3 .= '<tr class="hidden">';
                $out3 .= '<td>';
                $out3 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_current['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_current['size_index']) . ' in index.</p>';
                $out3 .= '<pre>' . $tbl_current['schema'] . '</pre>';
                $out3 .= '</td>';
                $out3 .= '<td>';
                $out3 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_snapshot['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_snapshot['size_index']) . ' in index.</p>';
                $out3 .= '<pre>' . $tbl_snapshot['schema'] . '</pre>';
                $out3 .= '</td>';
                $out3 .= '</tr>';
                $out3 .= '</table>';
                $out3 .= '</div>';
            } elseif ($schema1 != $schema2) {
                require_once $this->plugin_dir . 'libs/diff.php';
                require_once $this->plugin_dir . 'libs/diff/Renderer/Html/SideBySide.php';
                $diff = new WPR_Diff(explode("\n", $tbl_current['schema']), explode("\n", $tbl_snapshot['schema']), array('ignoreWhitespace' => false));
                $renderer = new WPR_Diff_Renderer_Html_SideBySide;

                $out2 .= '<div class="wpr-table-container" data-table="' . $tbl_current['basename'] . '">';
                $out2 .= '<table>';
                $out2 .= '<tr title="Click to show/hide more info" class="wpr-table-difference header-row">';
                $out2 .= '<td><b>' . $tbl_current['fullname'] . '</b> table schemas do not match</td>';
                $out2 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b> table schemas do not match<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
                $out2 .= '</tr>';
                $out2 .= '<tr class="hidden">';
                $out2 .= '<td>';
                $out2 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_current['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_current['size_index']) . ' in index.</p>';
                $out2 .= '</td>';
                $out2 .= '<td>';
                $out2 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_snapshot['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_snapshot['size_index']) . ' in index.</p>';
                $out2 .= '</td>';
                $out2 .= '</tr>';
                $out2 .= '<tr class="hidden">';
                $out2 .= '<td colspan="2" class="no-padding">';
                $out2 .= $diff->Render($renderer);
                $out2 .= '</td>';
                $out2 .= '</tr>';
                $out2 .= '</table>';
                $out2 .= '</div>';
            } else {
                $out2 .= '<div class="wpr-table-container" data-table="' . $tbl_current['basename'] . '">';
                $out2 .= '<table>';
                $out2 .= '<tr title="Click to show/hide more info" class="wpr-table-difference header-row">';
                $out2 .= '<td><b>' . $tbl_current['fullname'] . '</b> data in tables does not match</td>';
                $out2 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b> data in tables does not match<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
                $out2 .= '</tr>';
                $out2 .= '<tr class="hidden">';
                $out2 .= '<td>';
                $out2 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_current['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_current['size_index']) . ' in index.</p>';
                $out2 .= '</td>';
                $out2 .= '<td>';
                $out2 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . WP_Reset_Utility::format_size($tbl_snapshot['size_data']) . ' in data and ' . WP_Reset_Utility::format_size($tbl_snapshot['size_index']) . ' in index.</p>';
                $out2 .= '</td>';
                $out2 .= '</tr>';

                $out2 .= '<tr class="hidden">';
                $out2 .= '<td colspan="2">';
                if ($tbl_current['corename'] == 'options') {
                    $ss_prefix = $tbl_snapshot['uid'] . '_' . $wpdb->prefix;
                    $diff_rows = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$wpdb->prefix}options.option_value != {$ss_prefix}options.option_value LIMIT 100;");
                    $only_current = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$ss_prefix}options.option_value IS NULL LIMIT 100;");
                    $only_snapshot = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$wpdb->prefix}options.option_value IS NULL LIMIT 100;");
                    $out2 .= '<table class="table_diff">';
                    $out2 .= '<tr><td style="width: 100px;"><b>Option Name</b></td><td><b>Current Value</b></td><td><b>Snapshot Value</b></td></tr>';
                    foreach ($diff_rows as $row) {
                        $out2 .= '<tr>';
                        $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
                        $out2 .= '<td>' . (empty($row->current_value) ? '<i>empty</i>' : htmlentities($row->current_value)) . '</td>';
                        $out2 .= '<td>' . (empty($row->snapshot_value) ? '<i>empty</i>' : htmlentities($row->snapshot_value)) . '</td>';
                        $out2 .= '</tr>';
                    } // foreach
                    foreach ($only_current as $row) {
                        $out2 .= '<tr>';
                        $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
                        $out2 .= '<td>' . (empty($row->current_value) ? '<i>empty</i>' : htmlentities($row->current_value)) . '</td>';
                        $out2 .= '<td><i>not found in snapshot</i></td>';
                        $out2 .= '</tr>';
                    } // foreach
                    foreach ($only_current as $row) {
                        $out2 .= '<tr>';
                        $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
                        $out2 .= '<td><i>not found in current tables</i></td>';
                        $out2 .= '<td>' . (empty($row->snapshot_value) ? '<i>empty</i>' : htmlentities($row->snapshot_value)) . '</td>';
                        $out2 .= '</tr>';
                    } // foreach
                    $out2 .= '</table>';
                } else {
                    $out2 .= '<p class="textcenter">Detailed data diff is not available for this table.</p>';
                }
                $out2 .= '</td>';
                $out2 .= '</tr>';

                $out2 .= '</table>';
                $out2 .= '</div>';
            }
        } // foreach in both

        return $out . $out2 . $out3;
    } // do_compare_snapshots


    /**
     * Generates a unique 6-char snapshot ID; verified non-existing
     *
     * @return string
     */
    function generate_snapshot_uid()
    {
        global $wpdb;
        $snapshots = $this->get_snapshots();
        $cnt = 0;
        $uid = false;
        $uid_length = 6;
        if (strlen($wpdb->prefix) > 10) {
            $uid_length = 4;
        }

        do {
            $cnt++;
            $uid = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', $uid_length)), 0, $uid_length);

            $verify_db = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', array('%' . $uid . '%')));
        } while (!empty($verify_db) && isset($snapshots[$uid]) && $cnt < 30);

        if ($cnt == 30) {
            $uid = false;
        }

        return $uid;
    } // generate_snapshot_uid


    /**
     * Prune autosnapshots
     *
     * @return bool
     */
    function prune_autosnapshots($force = false)
    {
        $options = $this->get_all_options();

        if (!$force && ($options['options']['prune_snapshots'] != 1 || rand(0, 100) < 90)) {
            return false;
        }

        $snapshots = $this->get_snapshots();
        $snapshot_dates = array();
        $snapshot_sizes = array();
        $delete = array();

        foreach ($snapshots as $sid => $snapshot) {
            if (isset($snapshot['auto']) && $snapshot['auto'] == 'true') {
                $snapshot_dates[$sid] = strtotime($snapshot['timestamp']);
                $snapshot_sizes[$sid] = round(($snapshot['file_size'] + $snapshot['tbl_size']) / 10485.76) / 100; // Size in MB with 2 decimal places
            }
        }
        arsort($snapshot_dates);

        if (substr($options['options']['prune_snapshots_details'], 0, 4) == 'size') {
            $tmp = explode('-', $options['options']['prune_snapshots_details']);
            $max_size = (int) $tmp[1];
            $current_size = 0;
            foreach ($snapshot_sizes as $id => $size) {
                $current_size += $size;
                if ($current_size > $max_size) {
                    $delete[$sid] = $size;
                }
            }
        } else if (substr($options['options']['prune_snapshots_details'], 0, 3) == 'cnt') {
            $tmp = explode('-', $options['options']['prune_snapshots_details']);
            $tmp = (int) $tmp[1];
            $delete = array_slice($snapshot_dates, $tmp);
        } else {
            $tmp = explode('-', $options['options']['prune_snapshots_details']);
            $tmp = strtotime(current_time('mysql')) - ((int) $tmp[1] * 60 * 60 * 24);

            foreach ($snapshot_dates as $sid => $timestamp) {
                if ($timestamp < $tmp) {
                    $delete[$sid] = $timestamp;
                }
            }
        }

        if (count($delete) > 0) {
            foreach ($delete as $sid => $data) {
                $this->do_delete_snapshot($sid);
            }
        }
        return true;
    } // prune_autosnapshots


    /**
     * Register all hooks and filters for autosnapshots
     *
     * @return null
     */
    function register_autosnapshot_hooks()
    {
        if (!current_user_can('administrator') || !is_admin()) {
            return;
        }

        $options = $this->get_options();

        if (is_multisite() || $options['events_snapshots'] != '1') {
            return;
        }

        add_action('activate_plugin', array($this, 'autosnapshot_actions'));
        add_action('deactivate_plugin', array($this, 'autosnapshot_actions'));
        add_action('delete_plugin', array($this, 'autosnapshot_actions'));
        add_action('delete_theme', array($this, 'autosnapshot_actions'));
        add_filter('upgrader_pre_install', array($this, 'autosnapshot_update'), 10, 2);

        if (basename($_SERVER['SCRIPT_FILENAME'], '.php') == 'themes' && isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'activate' || $_REQUEST['action'] == 'delete') {
                $template = get_template_directory();
                $template_data = wp_get_theme(basename($template));
                $themes = array();
                $themes[$template_data['Name']] = $template;
                $stylesheet = get_stylesheet_directory();
                if ($stylesheet != $template) {
                    $stylesheet_data = wp_get_theme(basename($stylesheet));
                    $themes[$stylesheet_data['Name']] = $stylesheet;
                }
                $this->do_autosnapshot(array('action' => $_REQUEST['action'] . '_theme', 'type' => 'theme', 'themes' => $themes));
            }
        }

        if (basename($_SERVER['SCRIPT_FILENAME'], '.php') == 'admin-ajax' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete-theme') {
            global $wp_theme_directories;
            $template_data = wp_get_theme(basename($_REQUEST['slug']));
            $theme_root = get_raw_theme_root($_REQUEST['slug']);
            if (empty($theme_root)) {
                $theme_root = get_raw_theme_root($_REQUEST['slug']);
                if (false === $theme_root) {
                    $theme_root = WP_CONTENT_DIR . '/themes';
                } elseif (!in_array($theme_root, (array) $wp_theme_directories)) {
                    $theme_root = WP_CONTENT_DIR . $theme_root;
                }
            }
            $theme_path = WP_CONTENT_DIR . $theme_root . '/' . $_REQUEST['slug'];
            $themes = array();
            $themes[$template_data['Name']] = $theme_path;
            $this->do_autosnapshot(array('action' => 'delete_theme', 'type' => 'theme', 'themes' => $themes));
        }

        if (basename($_SERVER['SCRIPT_FILENAME'], '.php') == 'update-core' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'do-core-reinstall') {
            $this->do_autosnapshot(array('action' => 'reinstall_core', 'type' => 'core'));
        }

        if (basename($_SERVER['SCRIPT_FILENAME'], '.php') == 'update-core' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'do-core-upgrade') {
            $this->do_autosnapshot(array('action' => 'upgrade_core', 'type' => 'core'));
        }
    } // register_autosnapshot_hooks


    /**
     * Depending on auto snapshots setting for tools, creates a note or link to create snapshot
     *
     * @param string $description Default description for snapshot, if created manually
     *
     * @return string
     */
    function get_autosnapshot_tools_modal($description = '')
    {
        $options = $this->get_options();

        if ($options['tools_snapshots']) {
            return ' An automatic snapshot will be created so you can undo if needed.';
        } else {
            return htmlspecialchars(' Always <a data-snapshot-description="' . trim($description) . '" href="#" class="create-new-snapshot">make a snapshot</a> before running database related tools so you can undo if needed.');
        }
    } // get_autosnapshot_tools_modal


    /**
     * Verify what action is being done and call do_autosnapshot if needed
     *
     * @return null
     */
    function autosnapshot_actions()
    {
        $items = array();
        $items['action'] = current_action();
        $items['args'] = func_get_args();
        $items['type'] = 'plugin';

        $watched_actions = array('activate_plugin', 'deactivate_plugin', 'delete_plugin');

        if (empty($items['args'][0]) || !in_array($items['action'], $watched_actions)) {
            return false;
        }
        
        $plugin_path = plugin_dir_path(WP_PLUGIN_DIR . '/' . $items['args'][0]);
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $items['args'][0]);

        if($plugin_data['Name'] == 'WP Reset PRO'){
            return false;
        }

        $items['plugins'][$plugin_data['Name'] . ' v' . $plugin_data['Version']] = $plugin_path;

        if (isset($_REQUEST['checked'])) {
            foreach ($_REQUEST['checked'] as $plugin) {
                if (!in_array($plugin, $items['plugins'])) {
                    $plugin_path = plugin_dir_path(WP_PLUGIN_DIR . '/' . $plugin);
                    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                    $items['plugins'][$plugin_data['Name'] . ' v' . $plugin_data['Version']] = $plugin_path;
                }
            }
        }

        $this->do_autosnapshot($items);
    } // autosnapshot_actions


    /**
     * Check if plugin or theme is being updated and do_autosnapshot if needed
     *
     * @return null
     */
    function autosnapshot_update($return, $args)
    {
        if (is_wp_error($return)) {
            return $return;
        }

        $options = $this->get_all_options();
        if (is_multisite() || $options['options']['events_snapshots'] != '1') {
            return;
        }

        $items = array();
        $items['args'] = $args;
        $items['type'] = false;

        if (isset($items['args']['plugin'])) {
            if (!(strpos($items['args']['plugin'], 'wp-reset') === 0)) {
                $items['action'] = 'update_plugin';
                $items['type'] = 'plugin';
                $plugin_path = plugin_dir_path(WP_PLUGIN_DIR . '/' . $items['args']['plugin']);
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $items['args']['plugin']);
                $items['plugins'][$plugin_data['Name'] . ' v' . $plugin_data['Version']] = $plugin_path;
            }
        } else if (isset($items['args']['theme'])) {
            $items['action'] = 'update_theme';
            $items['type'] = 'themes';
            $template_data = wp_get_theme($items['args']['theme']);
            $items['themes'][$template_data['Name'] . ' v' . $template_data['Version']] = get_theme_root($items['args']['theme']) . '/' . $items['args']['theme'];
        }

        if ($items['type'] !== false) {
            if ($items['type'] == 'plugin' && isset($_REQUEST['plugins'])) {
                $plugins = explode(',', $_REQUEST['plugins']);
                foreach ($plugins as $plugin) {
                    if (!in_array($plugin, $items['plugins'])) {
                        $plugin_path = plugin_dir_path(WP_PLUGIN_DIR . '/' . $plugin);
                        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                        $items['plugins'][$plugin_data['Name'] . ' v' . $plugin_data['Version']] = $plugin_path;
                    }
                }
            }

            if ($items['type'] == 'themes' && isset($_REQUEST['themes'])) {
                $themes = explode(',', $_REQUEST['themes']);
                foreach ($themes as $theme) {
                    if (!in_array($theme, $items['themes'])) {
                        $template_data = wp_get_theme($theme);
                        $items['themes'][$template_data['Name'] . ' v' . $template_data['Version']] = get_theme_root($theme) . '/' . $theme;
                    }
                }
            }
            $this->do_autosnapshot($items);
        }

        return $return;
    } // autosnapshot_update


    /**
     * Get plugin path from slug
     *
     * @return string path
     */
    function get_plugin_path($slug)
    {
        $all_plugins = get_plugins();
        foreach ($all_plugins as $plugin_path => $plugin) {
            if (strpos($plugin_path, $slug . '/') === 0) {
                return $plugin_path;
            }
        }
        return false;
    } // get_plugin_path


    function clean_snapshot_name($name){
        return preg_replace('/[^a-z0-9_s+ \s{}\'\"\:\,\.\/]/i', '', $name);
    }

    function clean_snapshot_items($items){
        $cleaned_items = array();
        foreach($items as $name => $data){
            $cleaned_items[$this->clean_snapshot_name($name)] = $data;
        }
        return $cleaned_items;
    }

    /**
     * Crate snapshot and zip files
     *
     * @return null
     */
    function do_autosnapshot($items)
    {
        global $wp_version;
        $options = $this->get_all_options();
        if (isset($GLOBALS['wpr_autosnapshot_done'])) {
            return false;
        }
        $GLOBALS['wpr_autosnapshot_done'] = true;

        wp_cache_flush();

        switch ($items['action']) {
            case 'activate_plugin':
                if (count($items['plugins']) > 1) {
                    $snapshot['name'] = 'Before the following plugins were activated: ' . implode(', ', array_keys($items['plugins']));
                } else {
                    $snapshot['name'] = 'Before ' . implode(', ', array_keys($items['plugins'])) . ' was activated';
                }
                $snapshot['plugins'] = $this->clean_snapshot_items($items['plugins']);
                break;
            case 'deactivate_plugin':
                if (count($items['plugins']) > 1) {
                    $snapshot['name'] = 'Before the following plugins were deactivated: ' . implode(', ', array_keys($items['plugins']));
                } else {
                    $snapshot['name'] = 'Before ' . implode(', ', array_keys($items['plugins'])) . ' was deactivated';
                }
                $snapshot['plugins'] = $this->clean_snapshot_items($items['plugins']);
                break;
            case 'delete_plugin':
                if (count($items['plugins']) > 1) {
                    $snapshot['name'] = 'Before the following plugins were deleted: ' . implode(', ', array_keys($items['plugins']));
                } else {
                    $snapshot['name'] = 'Before ' . implode(', ', array_keys($items['plugins'])) . ' was deleted';
                }
                $snapshot['plugins'] = $this->clean_snapshot_items($items['plugins']);
                break;
            case 'update_plugin':
                if (count($items['plugins']) > 1) {
                    $snapshot['name'] = 'Before the following plugins were updated: ' . implode(', ', array_keys($items['plugins']));
                } else {
                    $update_plugins = get_site_transient('update_plugins');
                    if (isset($update_plugins->response[$items['args']['plugin']])) {
                        $new_version = 'v' . $update_plugins->response[$items['args']['plugin']]->new_version;
                    } else {
                        $new_version = 'an unknown version';
                    }
                    $snapshot['name'] = 'Before ' . implode(', ', array_keys($items['plugins'])) . ' was updated to ' . $new_version;
                }
                $snapshot['plugins'] = $this->clean_snapshot_items($items['plugins']);
                break;
            case 'update_theme':
                if (count($items['themes']) > 1) {
                    $snapshot['name'] = 'Before the following themes were updated: ' . implode(', ', array_keys($items['themes']));
                } else {
                    $update_themes = get_site_transient('update_themes');
                    if (isset($update_themes->response[$items['args']['theme']])) {
                        $new_version = 'v' . $update_themes->response[$items['args']['theme']]['new_version'];
                    } else {
                        $new_version = 'an unknown version';
                    }
                    $snapshot['name'] = 'Before ' . implode(', ', array_keys($items['themes'])) . ' was updated to ' . $new_version;
                }
                $snapshot['themes'] = $this->clean_snapshot_items($items['themes']);
                break;
            case 'activate_theme':
                $current_theme = wp_get_theme();
                $new_theme = wp_get_theme($_REQUEST['stylesheet']);
                $snapshot['name'] = 'Before theme ' . $current_theme['Name'] . ' was changed to ' . $new_theme['Name'];
                $snapshot['themes'] = $this->clean_snapshot_items($items['themes']);
                break;
            case 'delete_theme':
                reset($items['themes']);
                $snapshot['name'] = 'Before theme ' . key($items['themes']) . ' was deleted';
                $snapshot['themes'] = $this->clean_snapshot_items($items['themes']);
                break;
            case 'upgrade_core':
                if (!function_exists('get_core_updates')) {
                    require_once ABSPATH . 'wp-admin/includes/update.php';
                }
                $update_core = get_core_updates();
                $snapshot['name'] = 'Before WordPress was upgraded from ' . $wp_version . ' to ' . $update_core[0]->version;
                break;
            case 'reinstall_core':
                $snapshot['name'] = 'Before WordPress ' . $wp_version . ' was reinstalled';
                break;
        }

        $snapshot['name'] = $this->clean_snapshot_name($snapshot['name']);
        
        $snapshot['auto'] = true;

        $this->do_create_snapshot($snapshot);
    } // do_autosnapshot

    function get_db_info()
    {
        $options = $this->get_options();

        if($options['alternate_db_connection'] == true){
            return array('host' => DB_HOST, 'port' => false);
        } 
            
        if (strpos(DB_HOST, ':') > 0) {
            $db_host_parts = parse_url(DB_HOST);
            return array('host' => $db_host_parts['host'], 'port' => $db_host_parts['port']);
        } else {
            return array('host' => DB_HOST, 'port' => 3306);
        }
    }

    /**
     * Use when uninstalling (deleting) the plugin to clean up.
     *
     * @return bool
     */
    static function uninstall_plugin()
    {
        $tmp = delete_option('wf_licensing_wpr');

        return $tmp;
    } // uninstall_plugin

    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    private function __clone()
    {
    }


    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    private function __sleep()
    {
    }


    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    private function __wakeup()
    {
    }
} // WP_Reset class


// Create plugin instance and hook things up
global $wp_reset;
$wp_reset = WP_Reset::getInstance();
add_action('plugins_loaded', array($wp_reset, 'load_textdomain'));
add_action('plugins_loaded', array($wp_reset, 'register_autosnapshot_hooks'));
register_uninstall_hook(__FILE__, array('WP_Reset', 'uninstall_plugin'));
add_filter('upgrader_pre_install', array($wp_reset, 'autosnapshot_update'), 1, 2);
