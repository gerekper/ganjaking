<?php
/**
 * Plugin Name: WPMU DEV Dashboard
 * Plugin URI:  https://premium.wpmudev.org/project/wpmu-dev-dashboard/
 * Description: Brings the powers of WPMU DEV directly to you. It will revolutionize how you use WordPress. Activate now!
 * Author:      WPMU DEV
 * Version:     4.7.5
 * Author URI:  https://premium.wpmudev.org/
 * Text Domain: wpmudev
 * Domain Path: includes/languages/
 * Network:     true
 * WDP ID:      119
 *
 * @package WPMUDEV_Dashboard
 */

/*
Copyright 2007-2018 Incsub (http://incsub.com)
Author - Aaron Edwards
Contributors - Philipp Stracker, Victor Ivanov, Vladislav Bailovic, Jeffri H, Marko Miljus

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * The main Dashboard class that behaves as an interface to the other Dashboard
 * classes.
 */
class WPMUDEV_Dashboard {

	/**
	 * The current plugin version. Must match the plugin header.
	 *
	 * @var string (Version number)
	 */
	static public $version = '4.7.5';

	/**
	 * The current SUI version.
	 * @var string (SUI Version number)
	 *
	 * Required for the body class on admin pages
	 * Use sui followed by version number
	 * Use dash instead of dots as number seperator
	 */
	static public $sui_version = 'sui-2-5-2';

	/**
	 * Holds the API module.
	 * Handles all the remote calls to the WPMUDEV Server.
	 *
	 * @var   WPMUDEV_Dashboard_Api
	 * @since 4.0.0
	 */
	static $api = null;

	/**
	 * Holds the Remote module.
	 * Handles all the Hub calls from the WPMUDEV Servers.
	 *
	 * @var   WPMUDEV_Dashboard_Remote
	 * @since 4.0.0
	 */
	static $remote = null;

	/**
	 * Holds the Site/Settings module.
	 * Handles all local things like storing/fetching settings.
	 *
	 * @var   WPMUDEV_Dashboard_Site
	 * @since 4.0.0
	 */
	static $site = null;

	/**
	 * Holds the UI module.
	 * Handles all the UI tasks, like displaying a specific Dashboard page.
	 *
	 * @var   WPMUDEV_Dashboard_Ui
	 * @since 4.0.0
	 */
	static $ui = null;

	/**
	 * Holds the Upgrader module.
	 * Handles all upgrade/installation relevant tasks.
	 *
	 * @var   WPMUDEV_Dashboard_Upgrader
	 * @since 4.1.0
	 */
	static $upgrader = null;

	/**
	 * Holds the Notification module.
	 * Handles all the dashboard notifications.
	 *
	 * @var   WPMUDEV_Dashboard_Notice
	 * @since 4.0.0
	 */
	static $notice = null;

	/**
	 * Creates and returns the WPMUDEV Dashboard object.
	 * We'll have only one of those ;)
	 *
	 * Important: This function must be called BEFORE the plugins_loaded hook!
	 *
	 * @since  4.0.0
	 * @return WPMUDEV_Dashboard
	 */
	static public function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new WPMUDEV_Dashboard();
		}

		return $Inst;
	}

	/**
	 * The singleton constructor will initialize the modules.
	 *
	 * Important: This function must be called BEFORE the plugins_loaded hook!
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		require_once 'shared-ui/plugin-ui.php';

		require_once 'includes/class-wpmudev-dashboard-site.php';
		require_once 'includes/class-wpmudev-dashboard-api.php';
		require_once 'includes/class-wpmudev-dashboard-remote.php';
		require_once 'includes/class-wpmudev-dashboard-ui.php';
		require_once 'includes/class-wpmudev-dashboard-upgrader.php';
		require_once 'includes/class-wpmudev-dashboard-notice.php';

		self::$site     = new WPMUDEV_Dashboard_Site( __FILE__ );
		self::$api      = new WPMUDEV_Dashboard_Api();
		self::$remote   = new WPMUDEV_Dashboard_Remote();
		self::$notice   = new WPMUDEV_Dashboard_Message();
		self::$upgrader = new WPMUDEV_Dashboard_Upgrader();

		/*
		 * The UI module sets up all the WP hooks when it is created.
		 * So it should stay the last module to create, so it can access the
		 * other modules already in the constructor.
		 */

		self::$ui = new WPMUDEV_Dashboard_Ui();

		// Register the plugin activation hook.
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );

		// Register the plugin deactivation hook.
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );

		// Register the plugin uninstall hook.
		register_uninstall_hook( __FILE__, array( 'WPMUDEV_Dashboard', 'uninstall_plugin' ) );

		/**
		 * Custom code can be executed after Dashboard is initialized with the
		 * default settings.
		 *
		 * @since  4.0.0
		 * @var  WPMUDEV_Dashboard The initialized dashboard object.
		 */
		do_action( 'wpmudev_dashboard_init', $this );
	}

	/**
	 * Run code on plugin activation.
	 *
	 * @since  1.0.0
	 * @internal Action hook
	 */
	public function activate_plugin() {
		global $current_user;

		// Make sure all Dashboard settings exist in the DB.
		WPMUDEV_Dashboard::$site->init_options();

		// Reset the admin-user when plugin is activated.
		if ( $current_user && $current_user->ID ) {
			WPMUDEV_Dashboard::$site->set_option( 'limit_to_user', $current_user->ID );
		} else {
			WPMUDEV_Dashboard::$site->set_option( 'limit_to_user', '' );
		}

		// On next page load we want to redirect user to login page.
		WPMUDEV_Dashboard::$site->set_option( 'redirected_v4', 0 );

		// Force refresh of all data when plugin is activated.
		WPMUDEV_Dashboard::$site->set_option( 'refresh_profile_flag', 1 );
		add_action( 'shutdown', array( WPMUDEV_Dashboard::$api, 'refresh_projects_data' ) ); // this needs to trigger after init to prevent Call to undefined function wp_get_current_user() errors.
		WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
	}

	/**
	 * Run code on plugin deactivation.
	 *
	 * @since  4.1.1
	 * @internal Action hook
	 */
	public function deactivate_plugin() {
		// On next page load we want to redirect user to login page.
		WPMUDEV_Dashboard::$site->set_option( 'redirected_v4', 0 );
	}

	/**
	 * Run code on plugin uninstall.
	 *
	 * @since  4.5
	 * @internal Action hook
	 */
	public static function uninstall_plugin() {
		// On next page load we want to redirect user to login page.
		WPMUDEV_Dashboard::$site->logout( false );
		// TODO Delete all options from DB.
	}
};

// Initialize the WPMUDEV Dashboard.
WPMUDEV_Dashboard::instance();

if ( ! class_exists( 'WPMUDEV_Update_Notifications' ) ) {

	/**
	 * Dummy class for backwards compatibility to stone-age.
	 */
	class WPMUDEV_Update_Notifications {};
}
