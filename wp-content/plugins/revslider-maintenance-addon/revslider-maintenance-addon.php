<?php
/**
 * @link              https://www.themepunch.com
 * @since             1.0.0
 * @package           Revslider_Maintenance_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Slider Revolution Coming Soon & Maintenance Add-on
 * Plugin URI:        https://www.themepunch.com
 * Description:       Very simple Coming Soon & Maintenance Page by RevSlider
 * Version:           2.1.3
 * Author:            ThemePunch
 * Author URI:        https://www.themepunch.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       revslider-maintenance-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if(!defined('WPINC')){ die; }

define("REV_ADDON_MAINTENANCE_VERSION", "2.1.3");
define("REV_ADDON_MAINTENANCE_URL", str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
$rs_maintanence_script_added = false;

/**
 * New "verify/notices" setup for all Global Addons
 * @since    2.0.0
 */
function run_revslider_maintenance_addon() {
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Maintenance_Addon_Verify();
	if($verify->is_verified()) {
		
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-revslider-maintenance-addon.php';
		$plugin = new Revslider_Maintenance_Addon();
		$plugin->run();
		
	}
}
// run_revslider_maintenance_addon();
add_action('plugins_loaded', 'run_revslider_maintenance_addon');
register_activation_hook( __FILE__, 'run_revslider_maintenance_addon');
