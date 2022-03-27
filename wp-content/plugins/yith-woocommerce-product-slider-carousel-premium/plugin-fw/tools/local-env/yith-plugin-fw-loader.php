<?php
/**
 * Plugin Name: YITH Plugin Framework Loader
 * Plugin URI:
 * Description: YITH Plugin Framework Loader
 * Version: 1.0.0
 * Author: YITH
 * Author URI: http://yithemes.com/
 * Text Domain: yith-plugin-framework-loader
 * Domain Path: /languages/
 *
 * @author  YITH
 * @package YITH Plugin Framework Loader
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


add_action( 'plugins_loaded', 'yith_plugin_fw_loader_load_plugin_fw', 15 );

/**
 * Plugin Framework Loader.
 *
 * @return void
 */
function yith_plugin_fw_loader_load_plugin_fw() {

	if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
		global $plugin_fw_data;
		if ( ! empty( $plugin_fw_data ) ) {
			$plugin_fw_file = array_shift( $plugin_fw_data );
			require_once $plugin_fw_file;
		}
	}

}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
