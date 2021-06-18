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
 * @package           Revslider_Featured_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Slider Revolution Featured Slider Add-on
 * Plugin URI:        https://www.themepunch.com
 * Description:       Replace Posts' Featured Images with RevSliders if the theme allows it
 * Version:           2.0.3
 * Author:            ThemePunch
 * Author URI:        https://www.themepunch.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       revslider-featured-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("REV_ADDON_FEATURED_VERSION", "2.0.3");
define("REV_ADDON_FEATURED_URL", str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));


/**
 * New "verify/notices" setup for all Global Addons
 * @since    2.0.0
 */
function run_revslider_featured_addon() {
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Featured_Addon_Verify();
	if($verify->is_verified()) {
		
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-revslider-featured-addon.php';
		$plugin = new Revslider_Featured_Addon();
		$plugin->run();
		
	}

}


add_action('plugins_loaded', 'run_revslider_featured_addon');
register_activation_hook( __FILE__, 'run_revslider_featured_addon');
