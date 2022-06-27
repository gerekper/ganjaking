<?php
/*
Plugin Name: Perfmatters
Plugin URI: https://perfmatters.io/
Description: Perfmatters is a lightweight performance plugin developed to speed up your WordPress site.
Version: 1.9.4
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
define('PERFMATTERS_VERSION', '1.9.4');

function perfmatters_plugins_loaded() {

	//setup cache constants
	$perfmatters_cache_path = apply_filters('perfmatters_cache_path', 'cache');
	$host = parse_url(get_site_url())['host'];
	define('PERFMATTERS_CACHE_DIR', WP_CONTENT_DIR . '/' . $perfmatters_cache_path . "/perfmatters/$host/");
	define('PERFMATTERS_CACHE_URL', content_url('/') . $perfmatters_cache_path . "/perfmatters/$host/");

	//load translations
	load_plugin_textdomain('perfmatters', false, dirname(plugin_basename( __FILE__)) . '/languages/');

	//initialize plugin classes
	Perfmatters\Config::init();
	Perfmatters\Meta::init();

    //initialize classes that filter the buffer
    Perfmatters\Fonts::init();
    Perfmatters\Images::init();
    Perfmatters\Preload::init();
    Perfmatters\CSS::init();
    Perfmatters\CDN::init();
    Perfmatters\JS::init();

	Perfmatters\Buffer::init();

	new Perfmatters\DatabaseOptimizer();
}
add_action('plugins_loaded', 'perfmatters_plugins_loaded');

//initialize the updater
function perfmatters_edd_plugin_updater() {

	//to support auto-updates, this needs to run during the wp_version_check cron job for privileged users
	$doing_cron = defined('DOING_CRON') && DOING_CRON;
	if(!current_user_can('manage_options') && !$doing_cron) {
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

//plugin settings page header
function perfmatters_admin_header() {

	if(empty($_GET['page']) || $_GET['page'] !== 'perfmatters') {
		return;
	}

	$tab = !empty($_GET['tab']) ? $_GET['tab'] : (is_network_admin() ? 'network' : 'options');

	//header container
	echo '<div id="perfmatters-admin-header">';

		//logo
		echo '<svg id="perfmatters-logo" viewBox="0 0 81 73"><g transform="matrix(0.588901,0,0,0.588901,-5.75373,-2.93862)"><path d="M62.676,55.56C59.095,53.461 57.851,48.842 59.956,45.23C63.289,39.477 72.058,40.543 73.756,47.07C75.498,53.601 68.388,58.895 62.676,55.56M76.266,32.07C61.162,23.444 43.184,37.345 47.576,54.02C49.947,62.939 57.949,68.58 66.416,68.58C79.28,68.58 88.629,56.414 85.366,44C84.026,38.96 80.816,34.73 76.266,32.07M122.906,69.76L131.086,86.64C107.098,88.172 101.495,86.06 91.506,104.08C88.899,108.793 84.498,117.69 73.486,115.53C69.306,114.72 65.836,112.22 63.686,108.48L23.756,38.73C21.126,34.17 21.136,28.74 23.776,24.2C26.406,19.68 31.086,16.99 36.316,16.99C36.346,16.99 36.386,17 36.426,17L116.826,17.24C126.204,17.24 137.022,26.92 126.856,43.35C122.916,49.75 117.516,58.52 122.906,69.76M145.776,89.45L133.716,64.54C131.286,59.47 133.196,55.94 137.076,49.64C139.186,46.23 141.356,42.7 142.406,38.62C144.546,30.81 142.816,22.19 137.766,15.58C132.756,9.01 125.126,5.24 116.846,5.24C116.301,5.238 36.306,4.99 36.306,4.99C15.903,4.99 3.167,27.071 13.356,44.71L53.276,114.44C61.324,128.487 79.672,131.925 92.136,122.57C96.511,119.894 100.376,112.826 101.996,109.91C105.546,103.51 107.656,100.16 113.206,99.81L140.766,98.05C145.01,97.772 147.638,93.28 145.776,89.45M72.986,52.8C71.966,54.55 70.326,55.8 68.376,56.32C66.436,56.84 64.406,56.57 62.676,55.56C60.936,54.54 59.696,52.91 59.186,50.95C58.666,49 58.936,46.98 59.956,45.23C62.036,41.64 66.676,40.42 70.266,42.47C72.006,43.48 73.246,45.11 73.756,47.07C74.276,49.02 74.006,51.05 72.986,52.8M85.366,44C84.026,38.96 80.816,34.73 76.266,32.07C66.916,26.73 54.946,29.94 49.586,39.2C46.956,43.7 46.246,48.97 47.576,54.02C48.246,56.54 49.386,58.86 50.916,60.88C52.446,62.89 54.386,64.6 56.636,65.92C59.646,67.68 63.006,68.58 66.416,68.58C68.096,68.58 69.776,68.36 71.446,67.92C76.506,66.58 80.746,63.35 83.356,58.83C85.986,54.32 86.696,49.06 85.366,44M72.986,52.8C71.966,54.55 64.406,56.57 62.676,55.56C60.936,54.54 59.696,52.91 59.186,50.95C58.666,49 58.936,46.98 59.956,45.23C62.036,41.64 66.676,40.42 70.266,42.47C72.006,43.48 73.246,45.11 73.756,47.07C74.276,49.02 74.006,51.05 72.986,52.8M85.366,44C84.026,38.96 80.816,34.73 76.266,32.07C66.916,26.73 54.946,29.94 49.586,39.2C46.956,43.7 46.246,48.97 47.576,54.02C48.246,56.54 49.386,58.86 50.916,60.88C52.446,62.89 54.386,64.6 56.636,65.92C59.646,67.68 63.006,68.58 66.416,68.58C68.096,68.58 69.776,68.36 71.446,67.92C76.506,66.58 80.746,63.35 83.356,58.83C85.986,54.32 86.696,49.06 85.366,44" style="fill:#4A89DD;"/></g></svg>';
		echo '<div id="perfmatters-page-title">' . ucfirst($tab) . '</div>';

		//callout buttons
		echo '<div id="perfmatters-admin-header-buttons">';

			if(is_network_admin()) {
				echo '<a href="?page=perfmatters&tab=network" class="' . ($tab == 'network' || '' ? 'perfmatters-active' : '') . '" title="' . __('Network', 'perfmatters') . '">' . __('Network', 'perfmatters') . '</a>';
			}
			else {
				echo '<a href="?page=perfmatters&tab=options" class="' . ($tab == 'options' || '' ? 'perfmatters-active' : '') . '" title="' . __('Options', 'perfmatters') . '">' . __('Options', 'perfmatters') . '</a>';
				echo '<a href="?page=perfmatters&tab=tools" class="' . ($tab == 'tools' ? 'perfmatters-active' : '') . '" title="' . __('Tools', 'perfmatters') . '">' . __('Tools', 'perfmatters') . '</a>';
			}

			if(!is_plugin_active_for_network('perfmatters/perfmatters.php') || is_network_admin()) {
				echo '<a href="?page=perfmatters&tab=license" class="' . ($tab == 'license' ? 'perfmatters-active' : '') . '" title="' . __('License', 'perfmatters') . '">' . __('License', 'perfmatters') . '</a>';
			}

			echo '<a href="?page=perfmatters&tab=support" class="' . ($tab == 'support' ? 'perfmatters-active' : '') . '" title="' . __('Support', 'perfmatters') . '">' . __('Support', 'perfmatters') . '</a>';

			echo '<span style="color: rgba(255,255,255,0.5); margin: 0px 10px;" class="perfmatters-mobile-hide">v' . PERFMATTERS_VERSION . '</span>';

			echo '<a href="https://woorkup.com/speed-up-wordpress/?utm_source=perfmatters&utm_medium=banner&utm_campaign=header-cta" target="_blank" title="' . __('Speed Up Guide', 'perfmatters') . '" style="background: #fff; color: #282E34;" class="perfmatters-mobile-hide"><i class="dashicons dashicons-performance"></i>' . __('Speed Up Guide', 'perfmatters') . '</a>';
		echo '</div>';

	echo '</div>';
}
add_action('in_admin_header', 'perfmatters_admin_header', 1);

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
require_once plugin_dir_path(__FILE__) . 'inc/functions_lazy_load.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions_script_manager.php';
require_once plugin_dir_path(__FILE__) . 'inc/network.php';

//composer autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';