<?php

/**
 * Plugin Name: Happy Elementor Addons Pro
 * Plugin URI: https://happyaddons.com/
 * Description: <a href="https://happyaddons.com/">HappyAddons</a> is a collection of slick, powerful widgets that works seamlessly with Elementor page builder. It’s trendy look with detail customization features allows to create extraordinary designs instantly.
 * Version: 2.10.0
 * Author: Leevio
 * Author URI: https://happyaddons.com/
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: happy-addons-pro
 * Domain Path: /languages/
 *
 * @package Happy_Addons_Pro
 */

defined('ABSPATH') || die();

define('HAPPY_ADDONS_PRO_VERSION', '2.10.0');
define('HAPPY_ADDONS_PRO_REQUIRED_MINIMUM_VERSION', '3.7.0');
define('HAPPY_ADDONS_PRO__FILE__', __FILE__);
define('HAPPY_ADDONS_PRO_DIR_PATH', plugin_dir_path(HAPPY_ADDONS_PRO__FILE__));
define('HAPPY_ADDONS_PRO_DIR_URL', plugin_dir_url(HAPPY_ADDONS_PRO__FILE__));
define('HAPPY_ADDONS_PRO_ASSETS', trailingslashit(HAPPY_ADDONS_PRO_DIR_URL . 'assets'));


function hapro_plugin_updated($upgrader_object, $options) {
	$current_plugin_path_name = plugin_basename(__FILE__);

	$action = isset($options['action'])? $options['action']: '';
	$type = isset($options['type'])? $options['type']: '';
	$plugins = isset($options['plugins'])? (is_array($options['plugins'])? $options['plugins']: []): [];

	if ($action == 'update' && $type == 'plugin') {
		foreach ($plugins as $each_plugin) {
			if ($each_plugin == $current_plugin_path_name) {
				delete_option('hapro_used_skin_widgets');
			}
		}
	}
}

add_action('upgrader_process_complete', 'hapro_plugin_updated', 10, 2);

/**
 * The journey of a thousand miles starts here.
 *
 * @return void Some voids are not really void, you have to explore to figure out why not!
 */
function hapro_let_the_journey_begin() {

	/**
	 * Check for Happy Elementor Addons existence
	 * And prevent further execution if doesn't exist.
	 */
	// if (!is_plugin_active( 'happy-elementor-addons/plugin.php' )) {
	if ( !( in_array( 'happy-elementor-addons/plugin.php', (array) get_option( 'active_plugins', [] ), true ) ) ) {
		add_action('admin_notices', 'hapro_missing_happyaddons_notice');
		return;
	}

	/**
	 * Check for Happy Elementor Addons required version
	 * And prevent further execution if doesn't match.
	 */
	if (!version_compare(HAPPY_ADDONS_VERSION, HAPPY_ADDONS_PRO_REQUIRED_MINIMUM_VERSION, '>=')) {
		add_action('admin_notices', 'hapro_required_version_missing_notice');
		return;
	}

	/**
	 * Finally we got approval to load the Happy engine!
	 */
	include_once HAPPY_ADDONS_PRO_DIR_PATH . 'base.php';

	\Happy_Addons_Pro\Base::instance();
}

add_action('plugins_loaded', 'hapro_let_the_journey_begin', 20);

/**
 * Happy Elementor Addons missing notice for admin panel.
 *
 * @return void
 */
function hapro_missing_happyaddons_notice() {
	if (file_exists(WP_PLUGIN_DIR . '/happy-elementor-addons/plugin.php')) {
		$notice_title = __('Activate Happy Elementor Addons', 'happy-addons-pro');
		$notice_url   = wp_nonce_url('plugins.php?action=activate&plugin=happy-elementor-addons/plugin.php&plugin_status=all&paged=1', 'activate-plugin_happy-elementor-addons/plugin.php');
	} else {
		$notice_title = __('Install Happy Elementor Addons', 'happy-addons-pro');
		$notice_url   = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=happy-elementor-addons'), 'install-plugin_happy-elementor-addons');
	}

	$notice = sprintf(
		/* translators: 1: Plugin name 2: Happy Elementor Addons */
		esc_html__('%1$s requires %2$s to be installed and activated. Please %3$s', 'happy-addons-pro'),
		'<strong>' . esc_html__('Happy Elementor Addons Pro', 'happy-addons-pro') . '</strong>',
		'<strong>' . esc_html__('Happy Elementor Addons', 'happy-addons-pro') . '</strong>',
		'<a href="' . esc_url($notice_url) . '">' . esc_html($notice_title) . '</a>'
	);

	printf('<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $notice);
}

/**
 * Happy Elementor Addons version incompatibility notice for admin panel.
 *
 * @return void
 */
function hapro_required_version_missing_notice() {

	$notice_title = __('Update Happy Elementor Addons', 'happy-addons-pro');
    $notice_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=happy-elementor-addons/plugin.php'), 'upgrade-plugin_happy-elementor-addons/plugin.php');

	$notice = sprintf(
		/* translators: 1: Plugin name 2: Happy Elementor Addons 3: Required Happy Elementor Addons version */
		esc_html__('%1$s requires %2$s version %4$s or greater. %3$s', 'happy-addons-pro'),
		'<strong>' . esc_html__('Happy Elementor Addons Pro', 'happy-addons-pro') . '</strong>',
		'<strong>' . esc_html__('Happy Elementor Addons', 'happy-addons-pro') . '</strong>',
		'<a href="' . esc_url($notice_url) . '">' . $notice_title . '</a>',
		HAPPY_ADDONS_PRO_REQUIRED_MINIMUM_VERSION
	);

	printf('<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $notice);
}

/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 * @param $upgrader_object Array
 * @param $options Array
 */
function hapro_upgrade_completed($upgrader_object, $options) {
	// The path to our plugin's main file
	$hapro = plugin_basename(__FILE__);
	if ('update' == $options['action'] && 'plugin' == $options['type'] && isset($options['plugins'])) {
		foreach ($options['plugins'] as $plugin) {
			if ($plugin == $hapro) {
				flush_rewrite_rules();
			}
		}
	}
}
add_action('upgrader_process_complete', 'hapro_upgrade_completed', 10, 2);
