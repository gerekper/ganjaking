<?php
/**
 * @link              https://www.themepunch.com
 * @since             1.0.0
 * @package           Revslider_Sharing_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Slider Revolution Sharing Add-on
 * Plugin URI:        https://www.themepunch.com
 * Description:       Adds Social Sharing to Slider Revolution
 * Version:           3.0.1
 * Author:            ThemePunch
 * Author URI:        https://www.themepunch.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       revslider-sharing-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("REV_ADDON_SHARING_VERSION", "3.0.1");
define("REV_ADDON_SHARING_URL", str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));


/**
 * New "verify/notices" setup for all Global Addons
 * @since    2.0.0
 */
function run_revslider_sharing_addon() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Sharing_Addon_Verify();
	if($verify->is_verified()) {
			
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-revslider-sharing-addon.php';
		$plugin = new Revslider_Sharing_Addon();
		$plugin->run();
		
	}

}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'run_revslider_sharing_addon');
register_activation_hook( __FILE__, 'run_revslider_sharing_addon');
