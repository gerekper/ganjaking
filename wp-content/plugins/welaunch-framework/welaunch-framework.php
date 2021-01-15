<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing
/**
 * weLaunch, a simple, truly extensible and fully responsive options framework
 * for WordPress themes and plugins. Developed with WordPress coding
 * standards and PHP best practices in mind.
 *
 * Plugin Name:     weLaunch Framework
 * Plugin URI:      https://welaunch.io
 * Description:     Framework for weLaunch Plugins (this is a fork of Redux Plugin)
 * Author:          weLaunch.io
 * Author URI:      https://welaunch.io
 * Version:         1.0.1
 * Text Domain:     welaunch-framework
 * License:         GPLv3 or later
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Provides:        weLaunchFramework
 *
 * @package         weLaunchFramework
 * @author          weLaunch.io
 * @license         GNU General Public License, version 3
 * @copyright       2012-2020 weLaunch.io
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WELAUNCH_PLUGIN_FILE' ) ) {
	define( 'WELAUNCH_PLUGIN_FILE', __FILE__ );
}

require __DIR__ . '/vendor/autoload.php';

global $weLaunchLicenses;
if(is_multisite()) {
	$weLaunchLicenses = get_network_option(0, 'welaunch_licenses');
} else {
	$weLaunchLicenses = get_option('welaunch_licenses');
}

$weLaunchFrameworkUpdater = Puc_v4_Factory::buildUpdateChecker(
	'https://www.welaunch.io/updates/account/',
	__FILE__
);

$weLaunchFrameworkUpdater->addQueryArgFilter(function($args) {

    $args['plugin'] = basename(__FILE__, '.php');
    return $args;
});

// Require the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'class-welaunch-framework-plugin.php';

// Register hooks that are fired when the plugin is activated and deactivated, respectively.
register_activation_hook( __FILE__, array( 'weLaunch_Framework_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'weLaunch_Framework_Plugin', 'deactivate' ) );

// Get plugin instance.
weLaunch_Framework_Plugin::instance();

