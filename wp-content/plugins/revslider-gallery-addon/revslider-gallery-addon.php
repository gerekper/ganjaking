<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.themepunch.com
 * @since             1.0.1
 * @package           Rev_addon_gal
 *
 * @wordpress-plugin
 * Plugin Name:       Slider Revolution WP Gallery Add-On
 * Plugin URI:        http://revolution.themepunch.com
 * Description:       Replaces the WP Standard Gallery with the Revolution Sliders of your choice
 * Version:           2.0.2
 * Author:            ThemePunch
 * Author URI:        http://www.themepunch.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rev_addon_gal
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("REV_ADDON_GAL_VERSION", "2.0.2");
define("REV_ADDON_GAL_URL", str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));


/**
 * New "verify/notices" setup for all Global Addons
 * @since    2.0.0
 */
function run_rev_addon_gal() {
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Gallery_Addon_Verify();
	if($verify->is_verified()) {
		
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-rev_addon_gal.php';
		$plugin = new Rev_addon_gal();
		$plugin->run();
		
	}

}

// run_rev_addon_gal();
add_action('plugins_loaded', 'run_rev_addon_gal');
register_activation_hook( __FILE__, 'run_rev_addon_gal');
