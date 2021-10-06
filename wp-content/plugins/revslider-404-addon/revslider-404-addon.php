<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.themepunch.com
 * @since             1.0.0
 * @package           Revslider_404_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Slider Revolution 404 Page Add-On
 * Plugin URI:        https://revolution.themepunch.com
 * Description:       Build your own 404 Page with Slider Revolution!
 * Version:           2.0.1
 * Author:            ThemePunch
 * Author URI:        https://www.themepunch.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       revslider-404-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define("REV_ADDON_404_VERSION", "2.0.1");
define("REV_ADDON_404_URL", str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));


/**
 * New "verify/notices" setup for all Global Addons
 * @since    2.0.0
 */
function run_revslider_404_addon() {
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_404_Addon_Verify();
	if($verify->is_verified()) {
	
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-revslider-404-addon.php';
		$plugin = new Revslider_404_Addon();
		$plugin->run();
		
	}

}

register_activation_hook( __FILE__, 'run_revslider_404_addon');
add_action('plugins_loaded', 'run_revslider_404_addon');
