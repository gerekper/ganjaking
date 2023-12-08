<?php

namespace ElementPack;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly


require_once BDTEP_ADMIN_PATH . 'class-settings-api.php';
if (current_user_can('manage_options')) {
	require_once BDTEP_ADMIN_PATH . 'admin-feeds.php';
}
// element pack admin settings here
require_once BDTEP_ADMIN_PATH . 'admin-settings.php';

/**
 * Admin class
 */

class Admin {

	public function __construct() {

		// Embed the Script on our Plugin's Option Page Only

		add_action('admin_init', [$this, 'admin_script']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);


		add_action('upgrader_process_complete', [$this, 'bdthemes_element_pack_plugin_on_upgrade_process_complete'], 10, 2);

		register_deactivation_hook(BDTEP__FILE__, [$this, 'bdthemes_element_pack_plugin_on_deactivate']);

		add_action('after_setup_theme', [$this, 'whitelabel']);

		// register_activation_hook(BDTEP__FILE__, 'install_and_activate');

	}


	function install_and_activate() {

		// I don't know of any other redirect function, so this'll have to do.
		wp_redirect(admin_url('admin.php?page=element_pack_options'));
		// You could use a header(sprintf('Location: %s', admin_url(...)); here instead too.
	}

	/**
	 * You can easily add white label branding for extended license or multi site license. Don't try for regular license otherwise your license will be invalid.
	 * @return [type] [description]
	 * Define BDTEP_WL for execute white label branding
	 */
	public function whitelabel() {
		if (defined('BDTEP_WL')) {

			add_filter('gettext', [$this, 'element_pack_name_change'], 20, 3);

			if (defined('BDTEP_HIDE')) {
				add_action('pre_current_active_plugins', [$this, 'hide_element_pack']);
			}
		} else {
			add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
			add_filter('plugin_action_links_' . BDTEP_PBNAME, [$this, 'plugin_action_meta']);
		}
	}

	/**
	 * Enqueue styles
	 * @access public
	 */

	public function enqueue_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';
		$suffix           = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('bdt-uikit', BDTEP_ASSETS_URL . 'css/bdt-uikit' . $direction_suffix . '.css', [], '3.16.3');
		wp_enqueue_style('ep-editor', BDTEP_ASSETS_URL . 'css/ep-editor' . $direction_suffix . '.css', [], BDTEP_VER);

		wp_enqueue_style('ep-admin', BDTEP_ADMIN_URL . 'assets/css/ep-admin' . $direction_suffix . '.css', [], BDTEP_VER);
		wp_enqueue_script('bdt-uikit', BDTEP_ASSETS_URL . 'js/bdt-uikit' . $suffix . '.js', ['jquery'], '3.16.3');
	}

	/**
	 * Row meta
	 * @access public
	 * @return array
	 */

	public function plugin_row_meta($plugin_meta, $plugin_file) {
		if (BDTEP_PBNAME === $plugin_file) {
			$row_meta = [
				'docs'  => '<a href="https://elementpack.pro/contact/" aria-label="' . esc_attr(__('Go for Get Support', 'bdthemes-element-pack')) . '" target="_blank">' . __('Get Support', 'bdthemes-element-pack') . '</a>',
				'video' => '<a href="https://www.youtube.com/playlist?list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" aria-label="' . esc_attr(__('View Element Pack Video Tutorials', 'bdthemes-element-pack')) . '" target="_blank">' . __('Video Tutorials', 'bdthemes-element-pack') . '</a>',
			];

			$plugin_meta = array_merge($plugin_meta, $row_meta);
		}

		return $plugin_meta;
	}

	/**
	 * Action meta
	 * @access public
	 * @return array
	 */


	public function plugin_action_meta($links) {

		$links = array_merge([sprintf('<a href="%s">%s</a>', element_pack_dashboard_link('#element_pack_welcome'), esc_html__('Settings', 'bdthemes-element-pack'))], $links);

		$links = array_merge($links, [
			sprintf(
				'<a href="%s">%s</a>',
				element_pack_dashboard_link('#license'),
				esc_html__('License', 'bdthemes-element-pack')
			)
		]);

		return $links;
	}

	/**
	 * Change Element Pack Name
	 * @access public
	 * @return string
	 */

	public function element_pack_name_change($translated_text, $text, $domain) {
		switch ($translated_text) {
			case 'Element Pack Pro':
				$translated_text = BDTEP_TITLE;
				break;
		}

		return $translated_text;
	}

	/**
	 * Hiding plugins //still in testing purpose
	 * @access public
	 */

	public function hide_element_pack() {
		global $wp_list_table;
		$hide_plg_array = array('bdthemes-element-pack/bdthemes-element-pack.php');
		$all_plugins    = $wp_list_table->items;

		foreach ($all_plugins as $key => $val) {
			if (in_array($key, $hide_plg_array)) {
				unset($wp_list_table->items[$key]);
			}
		}
	}

	/**
	 * Register admin script
	 * @access public
	 */

	public function admin_script() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		if (is_admin()) { // for Admin Dashboard Only
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-form');

			if (isset($_GET['page']) && ($_GET['page'] == 'element_pack_options')) {
				wp_enqueue_script('chart', BDTEP_ASSETS_URL . 'vendor/js/chart.min.js', ['jquery'], '3.9.1', true);
				wp_enqueue_script('ep-admin', BDTEP_ADMIN_URL  . 'assets/js/ep-admin' . $suffix . '.js', ['jquery', 'chart'], BDTEP_VER, true);
			} else {
				wp_enqueue_script('ep-admin', BDTEP_ADMIN_URL  . 'assets/js/ep-admin' . $suffix . '.js', ['jquery'], BDTEP_VER, true);
			}
		}
	}

	/**
	 * Drop Tables on deactivated plugin
	 * @access public
	 */

	public function bdthemes_element_pack_plugin_on_deactivate() {

		global $wpdb;

		$table_cat      = $wpdb->prefix . 'ep_template_library_cat';
		$table_post     = $wpdb->prefix . 'ep_template_library_post';
		$table_cat_post = $wpdb->prefix . 'ep_template_library_cat_post';

		@$wpdb->query('DROP TABLE IF EXISTS ' . $table_cat_post);
		@$wpdb->query('DROP TABLE IF EXISTS ' . $table_cat);
		@$wpdb->query('DROP TABLE IF EXISTS ' . $table_post);
	}

	/**
	 * Upgrade Process Complete
	 * @access public
	 */

	public function bdthemes_element_pack_plugin_on_upgrade_process_complete($upgrader_object, $options) {
		if (isset($options['action']) && $options['action'] == 'update' && $options['type'] == 'plugin') {
			if (isset($options['plugins']) && is_array($options['plugins'])) {
				foreach ($options['plugins'] as $each_plugin) {
					if ($each_plugin == BDTEP_PBNAME) {
						@$this->bdthemes_element_pack_plugin_on_deactivate();
					}
				}
			}
		}
	}
}
