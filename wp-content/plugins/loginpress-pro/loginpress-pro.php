<?php
/*
* Plugin Name: LoginPress Pro
* Plugin URI: https://loginpress.pro/
* Description: This plugin adds premium features in your LoginPress plugin.
* Version: 2.5.3
* Author: WPBrigade
* Author URI: https://www.WPBrigade.com/
* License: GPLv2+
* Text Domain: loginpress-pro
* Domain Path: /languages
*/

define( 'LOGINPRESS_PRO_ROOT_PATH', dirname( __FILE__ ) );
define( 'LOGINPRESS_PRO_UPGRADE_PATH', __FILE__ );
define( 'LOGINPRESS_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'LOGINPRESS_PRO_THEME', LOGINPRESS_PRO_ROOT_PATH . '/themes/' );
define( 'LOGINPRESS_PRO_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOGINPRESS_PRO_PLUGIN_ROOT', dirname( plugin_basename( __FILE__ ) ) );

define( 'LOGINPRESS_PRO_STORE_URL', 'https://WPBrigade.com' );
define( 'LOGINPRESS_PRO_PRODUCT_NAME', 'LogingPress Pro' );
define( 'LOGINPRESS_PRO_VERSION', '2.5.3' );


add_action( 'plugins_loaded', 'loginpress_instance', 20 );

function loginpress_instance() {

	add_action( 'admin_enqueue_scripts', 'loginpress_pro_admin_action_scripts' );

	// Makes sure the plugin is defined before trying to use it
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	if ( is_multisite() && is_plugin_active_for_network( 'loginpress/loginpress.php' ) ) {
		// Plugin is activated.
	} elseif ( ! class_exists( 'LoginPress' ) ) {
		add_action( 'admin_menu', 'loginpress_pro_register_action_page' );
		return;
	}

	if ( ! class_exists( 'LoginPress' ) ) {
		add_action( 'admin_notices', 'lp_update_free' );
		return;
	}

	include_once LOGINPRESS_PRO_ROOT_PATH . '/classes/loginpress-main.php';
	new LoginPress_Pro();
}

function loginpress_pro_admin_action_scripts( $hook ) {

	if( $hook != 'toplevel_page_loginpress-settings' ) {
    return;
  }
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'loginpress-admin-action', plugins_url( 'js/admin-action.js', __FILE__ ), array( 'jquery' ), LOGINPRESS_PRO_VERSION );

	wp_localize_script(
		'loginpress-admin-action',
		'loginpress_pro_local',
		array(
			'admin_url' => admin_url( 'admin.php?page=loginpress-settings' ),
		)
	);

}

function loginpress_pro_register_action_page() {

	add_menu_page( __( 'LoginPress', 'loginpress' ), __( 'LoginPress', 'loginpress' ), 'manage_options', 'loginpress-settings', 'loginpress_pro_main_menu', plugins_url( 'loginpress/img/icon.svg' ), 50 );
}

function loginpress_pro_main_menu() {

	include_once LOGINPRESS_PRO_ROOT_PATH . '/includes/require-free.php';

}

function lp_update_free() {

	$action = 'upgrade-plugin';
	$slug   = 'loginpress';
	$link   = wp_nonce_url(
		add_query_arg(
			array(
				'action' => $action,
				'plugin' => $slug,
			),
			admin_url( 'update.php' )
		),
		$action . '_' . $slug
	);

	printf(
		'<div class="notice notice-error is-dismissible">
  <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>',
		esc_html__( 'Please update LoginPress to latest Free version to enable PRO features &mdash; ', 'loginpress-pro' ),
		$link,
		esc_html__( 'Update now', 'loginpress-pro' )
	);

}

register_deactivation_hook( __FILE__, 'loginpress_deactivate' );
function loginpress_deactivate() {
	update_option( 'customize_presets_settings', 'default1' );
}


/**
 * [loginpress_plugin_activation LoginPress (Free) Plugin Activation Callback]
 *
 * @since 2.0.7
 * @version 2.1.6
 */
function loginpress_plugin_activation() {

	check_ajax_referer( 'active_free', '_wpnonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'No cheating, huh!' );
  }

  $plugin = esc_html( $_POST['path'] );

	if ( ! is_plugin_active( $plugin ) ) {
		activate_plugin( $plugin );
  }

	wp_die();
}
add_action( 'wp_ajax_loginpress_activate_free', 'loginpress_plugin_activation' );
