<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://welaunch.io/plugins/wordpress-gdpr/
 * @since             1.0.0
 * @package           WordPress_GDPR
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress GDPR
 * Plugin URI:        https://welaunch.io/plugins/wordpress-gdpr/
 * Description:       EU-DSVGO, GDPR Compliance Plugin
 * Version:           1.9.32
 * Author:            weLaunch
 * Author URI:        https://welaunch.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-gdpr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-gdpr-activator.php
 */
function activate_WordPress_GDPR() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-gdpr-activator.php';
	$activator = new WordPress_GDPR_Activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-gdpr-deactivator.php
 */
function deactivate_WordPress_GDPR() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-gdpr-deactivator.php';
	WordPress_GDPR_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WordPress_GDPR' );
register_deactivation_hook( __FILE__, 'deactivate_WordPress_GDPR' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-gdpr.php';

/**
 * Run the Plugin
 * @author Daniel Barenkamp
 * @version 1.0.0
 * @since   1.0.0
 * @link    http://www.welaunch.io
 */
function run_WordPress_GDPR() {

	if(!isset($_COOKIE["wordpress_gdpr_cookies_allowed"])) {
		define('WP_TESTS_DOMAIN', true);
	}

	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new WordPress_GDPR($version);
	$plugin->run();

}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active('welaunch-framework/welaunch-framework.php') || is_plugin_active('redux-framework/redux-framework.php') || is_plugin_active('redux-dev-master/redux-framework.php') ){
	run_WordPress_GDPR();	
} else {
	add_action( 'admin_notices', 'run_WordPress_GDPR_Not_Installed' );
}

function run_WordPress_GDPR_Not_Installed()
{
	?>
    <div class="error">
      <p><?php _e( 'WordPress GDPR requires the weLaunch Framework Please install or activate it before: https://www.welaunch.io/updates/welaunch-framework.zip', 'wordpress-gdpr'); ?></p>
    </div>
    <?php
}