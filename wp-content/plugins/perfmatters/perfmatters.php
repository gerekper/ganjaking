<?php
/*
Plugin Name: Perfmatters
Plugin URI: https://perfmatters.io/
Description: Perfmatters is a lightweight performance plugin developed to speed up your WordPress site.
Version: 2.1.6
Author: forgemedia
Author URI: https://forgemedia.io/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: perfmatters
Domain Path: /languages
*/

/*****************************************************************************************
* EDD License
*****************************************************************************************/
define('PERFMATTERS_STORE_URL', 'https://perfmatters.io/');
define('PERFMATTERS_ITEM_ID', 696);
define('PERFMATTERS_ITEM_NAME', 'perfmatters');
define('PERFMATTERS_VERSION', '2.1.6');

function perfmatters_plugins_loaded() {

	//setup cache constants
	$perfmatters_cache_path = apply_filters('perfmatters_cache_path', 'cache');
	$parsed_url = parse_url(get_site_url());
	$host = $parsed_url['host'] . ($parsed_url['path'] ?? '');
	if(!defined('PERFMATTERS_CACHE_DIR')) {
		define('PERFMATTERS_CACHE_DIR', WP_CONTENT_DIR . '/' . $perfmatters_cache_path . "/perfmatters/$host/");
	}
	if(!defined('PERFMATTERS_CACHE_URL')) {
		define('PERFMATTERS_CACHE_URL', str_replace('http:', 'https:', content_url('/')) . $perfmatters_cache_path . "/perfmatters/$host/");
	}

	//load translations
	load_plugin_textdomain('perfmatters', false, dirname(plugin_basename( __FILE__)) . '/languages/');

	//initialize plugin classes
	Perfmatters\Config::init();
	Perfmatters\Meta::init();

	//initialize classes that filter the buffer
    Perfmatters\Fonts::init();
    Perfmatters\Images::init();
    Perfmatters\CSS::init();
	Perfmatters\LazyLoad::init_iframes();
    Perfmatters\Preload::init();
    Perfmatters\LazyLoad::init_images();
    Perfmatters\CDN::init();
    Perfmatters\JS::init();
	Perfmatters\Buffer::init();

	//initialize db optimizer
	new Perfmatters\DatabaseOptimizer();

	//initialize ajax
	new Perfmatters\Ajax();
}
add_action('plugins_loaded', 'perfmatters_plugins_loaded');

//setup cli commands
if(defined('WP_CLI' ) && WP_CLI) {
	require_once plugin_dir_path(__FILE__) . 'inc/CLI.php';
	function perfmatters_cli_register_commands() {
		WP_CLI::add_command('perfmatters', 'Perfmatters\CLI');
	}
	add_action('cli_init', 'perfmatters_cli_register_commands');
}

//initialize the updater
function perfmatters_edd_plugin_updater() {

	//to support auto-updates, this needs to run during the wp_version_check cron job for privileged users
	$doing_cron = defined('DOING_CRON') && DOING_CRON;
	if(!current_user_can('manage_options') && !$doing_cron && !defined('WP_CLI')) {
		return;
	}

	//retrieve our license key from the DB
	$license_key = is_multisite() ? trim(get_site_option('perfmatters_edd_license_key')) : trim(get_option('perfmatters_edd_license_key'));
	
	//setup the updater
	$edd_updater = new Perfmatters_Plugin_Updater(PERFMATTERS_STORE_URL, __FILE__, array(
			'version' 	=> PERFMATTERS_VERSION,
			'license' 	=> $license_key,
			'item_id'   => PERFMATTERS_ITEM_ID,
			'author' 	=> 'forgemedia',
			'beta'      => false
		)
	);
}
add_action('init', 'perfmatters_edd_plugin_updater', 0);

//add our admin menus
if(is_admin()) {
	add_action('admin_menu', 'perfmatters_menu', 9);
}

global $perfmatters_settings_page;

//admin menu
function perfmatters_menu() {
	if(perfmatters_network_access()) {
		global $perfmatters_settings_page;
		$perfmatters_settings_page = add_options_page('perfmatters', 'Perfmatters', 'manage_options', 'perfmatters', 'perfmatters_admin');
		add_action('load-' . $perfmatters_settings_page, 'perfmatters_settings_load');
	}
}

//admin settings page
function perfmatters_admin() {
	include plugin_dir_path(__FILE__) . '/inc/admin.php';
}

//admin settings page load hook
function perfmatters_settings_load() {
	add_action('admin_enqueue_scripts', 'perfmatters_admin_scripts');
}

//plugin admin scripts
function perfmatters_admin_scripts() {
	if(perfmatters_network_access()) {
		wp_register_style('perfmatters-styles', plugins_url('/css/style.css', __FILE__), array(), PERFMATTERS_VERSION);
		wp_enqueue_style('perfmatters-styles');

		wp_register_script('perfmatters-js', plugins_url('/js/perfmatters.js', __FILE__), array(), PERFMATTERS_VERSION);
		wp_enqueue_script('perfmatters-js');
		wp_localize_script('perfmatters-js', 'PERFMATTERS', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('perfmatters-nonce'),
			'strings' => array(
				'failed' => __('Action failed.', 'perfmatters')
			)
		));

		if(empty($_GET['tab']) || $_GET['tab'] == 'options') {
			$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html'));
			wp_localize_script('jquery', 'cm_settings', $cm_settings);
			wp_enqueue_script('wp-theme-plugin-editor');
			wp_enqueue_style('wp-codemirror');
		}
	}
}

//check multisite and verify access
function perfmatters_network_access() {
	if(is_multisite()) {
		$perfmatters_network = get_site_option('perfmatters_network');
		if((!empty($perfmatters_network['access']) && $perfmatters_network['access'] == 'super') && !is_super_admin()) {
			return false;
		}
	}
	return true;
}

//license messages in plugins table
function perfmatters_meta_links($links, $file) {
	if(strpos($file, 'perfmatters.php') !== false) {

		//support link
		$perfmatters_links = array('<a href="https://perfmatters.io/docs/" target="_blank">' . __('Support', 'perfmatters') . '</a>');

		$links = array_merge($links, $perfmatters_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'perfmatters_meta_links', 10, 2);

//settings link in plugins table
function perfmatters_action_links($actions, $plugin_file) 
{
	if(plugin_basename(__FILE__) == $plugin_file) {
		$settings_url = is_network_admin() ? network_admin_url('settings.php?page=perfmatters') : admin_url('options-general.php?page=perfmatters');
		$settings_link = array('settings' => '<a href="' . $settings_url . '">' . __('Settings', 'perfmatters') . '</a>');
		$actions = array_merge($settings_link, $actions);
	}
	return $actions;
}
add_filter('plugin_action_links', 'perfmatters_action_links', 10, 5);

//display message with plugin update if theres no valid license
function perfmatters_plugin_update_message() {

	$license_status = is_multisite() ? get_site_option('perfmatters_edd_license_status') : get_option('perfmatters_edd_license_status');

	if(empty($license_status) || $license_status !== 'valid') {
		echo ' <strong><a href="' . esc_url(admin_url('options-general.php?page=perfmatters&tab=license')) . '">' . __('Enter valid license key for automatic updates.', 'perfmatters') . '</a></strong>';
	}
}
add_action('in_plugin_update_message-perfmatters/perfmatters.php', 'perfmatters_plugin_update_message', 10, 2);

function perfmatters_activate() {
	
	//enable local analytics scheduled event
	$perfmatters_options = get_option('perfmatters_options');
	if(!empty($perfmatters_options['analytics']['enable_local_ga'])) {
		if(!wp_next_scheduled('perfmatters_update_ga')) {
			wp_schedule_event(time(), 'daily', 'perfmatters_update_ga');
		}
	}

	//check if we need to copy mu plugin file
	$pmsm_settings = get_option('perfmatters_script_manager_settings');
	if(!empty($pmsm_settings['mu_mode']) && !file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {
		if(file_exists(plugin_dir_path(__FILE__) . "/inc/perfmatters_mu.php")) {
			@copy(plugin_dir_path(__FILE__) . "/inc/perfmatters_mu.php", WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
		}
	}
}
register_activation_hook(__FILE__, 'perfmatters_activate');

//register a license deactivation
function perfmatters_deactivate() {

	//remove scheduled events
	foreach(array('perfmatters_update_ga', 'perfmatters_database_optimization') as $hook) {
		if(wp_next_scheduled($hook)) {
			wp_clear_scheduled_hook($hook);
		}
	}
}
register_deactivation_hook(__FILE__, 'perfmatters_deactivate');

//install plugin data
function perfmatters_install() {

	if(!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

	//mu plugin file check
	if(file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {

		//get plugin data
    	$mu_plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");

		if(!empty($mu_plugin_data['Version']) && $mu_plugin_data['Version'] != PERFMATTERS_VERSION) {
			@unlink(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
			if(file_exists(plugin_dir_path(__FILE__) . "/inc/perfmatters_mu.php")) {
				@copy(plugin_dir_path(__FILE__) . "/inc/perfmatters_mu.php", WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
			}
		}
	}

	$perfmatters_version = get_option('perfmatters_version');

	//migrate data to new locations
	if($perfmatters_version < '1.7.5') {

		//migration fields array
		$migration_fields = array(
			'perfmatters_options' => array(
				'perfmatters_options' => array(
					'lazy_loading'                => 'lazyload',
					'lazy_loading_iframes'        => 'lazyload',
					'youtube_preview_thumbnails'  => 'lazyload',
					'lazy_loading_exclusions'     => 'lazyload',
					'lazy_loading_dom_monitoring' => 'lazyload',
					'disable_google_fonts'        => 'fonts'
				)
			),
			'perfmatters_cdn' => array(
				'perfmatters_options' => array(
					'enable_cdn'      => 'cdn',
					'cdn_url'         => 'cdn',
					'cdn_directories' => 'cdn',
					'cdn_exclusions'  => 'cdn'
				)
			),
			'perfmatters_ga' => array(
				'perfmatters_options' => array(
					'enable_local_ga'          => 'analytics',
					'tracking_id'              => 'analytics',
					'tracking_code_position'   => 'analytics',
					'script_type'              => 'analytics',
					'disable_display_features' => 'analytics',
					'anonymize_ip'             => 'analytics',
					'track_admins'             => 'analytics',
					'adjusted_bounce_rate'     => 'analytics',
					'cdn_url'                  => 'analytics',
					'use_monster_insights'     => 'analytics',
					'enable_amp'               => 'analytics'
				)
			),
			'perfmatters_extras' => array(
				'perfmatters_options' => array(
					'blank_favicon'  => '',
					'script_manager' => 'assets',
					'defer_js'       => 'assets',
					'defer_jquery'   => 'assets',
					'js_exclusions'  => 'assets',
					'delay_js'       => 'assets',
					'delay_timeout'  => 'assets',
					'header_code'    => 'assets',
					'body_code'      => 'assets',
					'footer_code'    => 'assets',
					'instant_page'   => 'preload',
					'preload'        => 'preload',
					'preconnect'     => 'preload',
					'dns_prefetch'   => 'preload'
				),
				'perfmatters_tools' => array(
					'clean_uninstall'    => '',
					'accessibility_mode' => '',
					'post_revisions'     => 'database',
					'post_auto_drafts'   => 'database',
					'trashed_posts'      => 'database',
					'spam_comments'      => 'database',
					'trashed_comments'   => 'database',
					'expired_transients' => 'database',
					'all_transients'     => 'database',
					'tables'             => 'database',
					'optimize_schedule'  => 'database'
				)
			)
		);

		//loop through and migrate old data to new options
		foreach($migration_fields as $old_option_id => $new_options) {

			//old option
			$old_option_array = get_option($old_option_id, array());

			foreach($new_options as $new_option_id => $fields) {

				//new option
				$new_option_array = get_option($new_option_id, array());

				foreach($fields as $id => $section) {
					if(!empty($old_option_array[$id])) {
						if(empty($section)) {
							$new_option_array[$id] = $old_option_array[$id];
						}
						else {
							$new_option_array[$section][$id] = $old_option_array[$id];
						}
					}
				}

				//save new option
				update_option($new_option_id, $new_option_array);
			}
		}
	}

	if($perfmatters_version < '1.7.6') {

		$update_flag = false;

		$perfmatters_options = get_option('perfmatters_options');

		if(!empty($perfmatters_options['assets']['delay_js'])) {
			$perfmatters_options['assets']['delay_js_inclusions'] = $perfmatters_options['assets']['delay_js'];
			$perfmatters_options['assets']['delay_js'] = '1';
			$update_flag = true;
		}

		if($update_flag) {
			update_option('perfmatters_options', $perfmatters_options);
		}
	}

	if($perfmatters_version < '2.1.1') {

		$perfmatters_options = get_option('perfmatters_options');
		$perfmatters_tools = get_option('perfmatters_tools');

		if(!empty($perfmatters_options['assets']['defer_jquery']) && empty($perfmatters_tools['show_advanced'])) {
			$perfmatters_tools['show_advanced'] = '1';
			update_option('perfmatters_tools', $perfmatters_tools);
		}
	}

	//update version
	if($perfmatters_version != PERFMATTERS_VERSION) {
		update_option('perfmatters_version', PERFMATTERS_VERSION, false);
	}

	//update network version if needed
	if(is_multisite()) {
		if(get_site_option('perfmatters_version') != PERFMATTERS_VERSION) {
			update_site_option('perfmatters_version', PERFMATTERS_VERSION, false);
		}
	}
}

//check version for update
function perfmatters_version_check() {
	$install_flag = false;
	if(is_multisite()) {
		if(get_site_option('perfmatters_version') != PERFMATTERS_VERSION) {
	    	$install_flag = true;
	    }
	}
	if(get_option('perfmatters_version') != PERFMATTERS_VERSION) {
    	$install_flag = true;
    }
	if($install_flag) {
		perfmatters_install();
	}
}
add_action('plugins_loaded', 'perfmatters_version_check');

//uninstall plugin + delete options
function perfmatters_uninstall() {

	//deactivate license if needed
	perfmatters_deactivate_license();

	//plugin options
	$perfmatters_options = array(
		'perfmatters_options',
		'perfmatters_cdn', //deprecated
		'perfmatters_ga', //deprecated
		'perfmatters_extras', //deprecated
		'perfmatters_tools',
		'perfmatters_used_css_time',
		'perfmatters_script_manager',
		'perfmatters_script_manager_settings',
		'perfmatters_edd_license_key',
		'perfmatters_edd_license_status',
		'perfmatters_version'
	);

	//meta options
	$perfmatters_meta_options = array(
		'perfmatters_exclude_defer_js',
		'perfmatters_exclude_lazy_loading',
		'perfmatters_exclude_instant_page'
	);

	if(is_multisite()) {
		$perfmatters_network = get_site_option('perfmatters_network');
		if(!empty($perfmatters_network['clean_uninstall']) && $perfmatters_network['clean_uninstall'] == 1) {

			global $wpdb;

			//remove network option
			delete_site_option('perfmatters_network');

			$sites = array_map('get_object_vars', get_sites(array('deleted' => 0)));
			if(is_array($sites) && $sites !== array()) {
				foreach($sites as $site) {

					//remove options
					foreach($perfmatters_options as $option) {
						delete_blog_option($site['blog_id'], $option);
					}

					//remove meta options
					foreach($perfmatters_meta_options as $option) {
						$wpdb->delete($wpdb->get_blog_prefix($site['blog_id']) . 'postmeta', array('meta_key' => $option));
					}
				}
			}

			//remove stored version
			delete_site_option('perfmatters_version');
		}
	}
	else {
		$perfmatters_tools = get_option('perfmatters_tools');
		if(!empty($perfmatters_tools['clean_uninstall']) && $perfmatters_tools['clean_uninstall'] == 1) {

			global $wpdb;

			//remove options
			foreach($perfmatters_options as $option) {
				delete_option($option);
			}

			//remove meta options
			foreach($perfmatters_meta_options as $option) {
				$wpdb->delete($wpdb->prefix . 'postmeta', array('meta_key' => $option));
			}

			//remove stored version
         	delete_option('perfmatters_version');
		}
	}

	//remove cache directory if needed
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
	if(class_exists('WP_Filesystem_Direct')) {
		$fileSystemDirect = new WP_Filesystem_Direct(false);
		$cache_dir = ABSPATH . 'wp-content/cache/perfmatters';
		if($fileSystemDirect->is_dir($cache_dir)) {
			$fileSystemDirect->rmdir($cache_dir, true);
		}
	}

	//remove mu plugin file if needed
	if(file_exists(WPMU_PLUGIN_DIR . "/perfmatters_mu.php")) {
   		@unlink(WPMU_PLUGIN_DIR . "/perfmatters_mu.php");
   	}
}
register_uninstall_hook(__FILE__, 'perfmatters_uninstall');

//main file includes
require_once plugin_dir_path(__FILE__) . 'EDD_SL_Plugin_Updater.php';
require_once plugin_dir_path(__FILE__) . 'inc/settings.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions_script_manager.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions_network.php';

//composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';