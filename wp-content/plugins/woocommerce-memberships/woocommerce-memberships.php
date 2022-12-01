<?php
/**
 * Plugin Name: WooCommerce Memberships
 * Plugin URI: https://www.woocommerce.com/products/woocommerce-memberships/
 * Documentation URI: https://docs.woocommerce.com/document/woocommerce-memberships/
 * Description: Sell memberships that provide access to restricted content, products, discounts, and more!
 * Author: SkyVerge
 * Author URI: https://www.woocommerce.com/
 * Version: 1.24.0
 * Text Domain: woocommerce-memberships
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2014-2022 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 958589:9288e7609ad0b487b81ef6232efa5cfc
 * WC requires at least: 3.9.4
 * WC tested up to: 7.1.0
 */

defined( 'ABSPATH' ) or exit;

// Load required Action Scheduler library:
// during deploy of 1.16.0 Action Scheduler we accidentally bundled AS 3.0.0-beta-1 and some customers may have migrated: these customers need to continue using version 3.0.0 as they can't roll back to 2.x
// TODO: when WooCommerce 4.0 is the minimum required version we can stop bundling Action Scheduler 3.0 for installations that are currently using a WooCommerce version between 3.5 and 4.0
$load_bundled_as_3_0 = $as_table_name = false;

// if this flag was set during 1.16.2 update, then Action Scheduler 3.0 may have to be loaded for WooCommerce versions below 4.0
if ( 'yes' === get_option( 'wc_memberships_use_as_3_0_0' ) ) {

	// load latest Action Scheduler if using a WooCommerce version < 4.0, otherwise let WooCommerce load the latest bundle
	$load_bundled_as_3_0 = version_compare( get_option( 'woocommerce_version', '3.5' ), '4.0', '<' );

	if ( ! $load_bundled_as_3_0 ) {
		update_option( 'wc_memberships_use_as_3_0_0', 'no' );
	}

// check if updating from one of the affected memberships versions before 1.16.2
} elseif ( in_array( get_option( 'wc_memberships_version' ), array( '1.16.0', '1.16.1' ), false ) ) {
	global $wpdb;

	$as_table_name = $wpdb->prefix . 'actionscheduler_actions';

	// skip if there is no Action Scheduler table: migration hasn't started
	if ( $as_table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$as_table_name}'" ) ) {

		update_option( 'wc_memberships_use_as_3_0_0', 'yes' );

		$load_bundled_as_3_0 = true;

		// check if data was only partially migrated by looking if there is at least one scheduled action post
		if ( $load_bundled_as_3_0 && $wpdb->get_row( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'scheduled-action'" ) ) {
			// deleting this option should trigger data migration again
			delete_option( 'action_scheduler_migration_status' );
		}

	} else {

		update_option( 'wc_memberships_use_as_3_0_0', 'no' );
	}
}

if ( $load_bundled_as_3_0 ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/prospress/action-scheduler/action-scheduler.php' );
} else {
	require_once( plugin_dir_path( __FILE__ ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php' );
}

unset( $load_bundled_as_3_0, $as_table_name );


/**
 * WooCommerce Memberships plugin loader.
 *
 * @since 1.11.0
 */
class WC_Memberships_Loader {


	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.4';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.6';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.9.4';

	/** SkyVerge plugin framework version used by this plugin */
	const FRAMEWORK_VERSION = '5.10.13';

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'WooCommerce Memberships';


	/** @var \WC_Memberships_Loader single instance of this class */
	protected static $instance;

	/** @var array the admin notices to add */
	protected $notices = array();


	/**
	 * Loads WooCommerce Memberships after performing environment checks.
	 *
	 * @since 1.11.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init',    array( $this, 'check_environment' ) );
		add_action( 'admin_init',    array( $this, 'add_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_filter( 'extra_plugin_headers', array( $this, 'add_documentation_header' ) );

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {

			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.11.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.11.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.11.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.11.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_plugin() {

		if ( ! $this->plugins_compatible() ) {
			return;
		}

		$this->load_framework();

		// load the main plugin class
		require_once( plugin_dir_path( __FILE__ ) . 'class-wc-memberships.php' );

		// fire it up!
		wc_memberships();
	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 1.11.0
	 */
	protected function load_framework() {

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Plugin' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php' );
		}

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WP_Async_Request' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/utilities/class-sv-wp-async-request.php' );
		}

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WP_Background_Job_Handler' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/utilities/class-sv-wp-background-job-handler.php' );
		}
	}


	/**
	 * Returns the framework version in namespace form.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	protected function get_framework_version_namespace() {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Returns the framework version used by this plugin.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	protected function get_framework_version() {

		return self::FRAMEWORK_VERSION;
	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function add_plugin_notices() {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%s requires WooCommerce version %s or higher. Please %supdate WooCommerce &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}


	/**
	 * Determines if the active WooCommerce version is compatible with Memberships.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.11.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.11.0
	 *
	 * @param string $slug notice ID
	 * @param string $class CSS class
	 * @param string $message message content
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}


	/**
	 * Displays any admin notices added with \WC_Memberships_Loader::add_admin_notice()
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) :

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
			</div>
			<?php

		endforeach;
	}


	/**
	 * Adds the Documentation URI header.
	 *
	 * @internal
	 *
	 * @since 1.17.6-dev.1
	 *
	 * @param string[] $headers original headers
	 * @return string[]
	 */
	public function add_documentation_header( $headers ) {

		$headers[] = 'Documentation URI';

		return $headers;
	}


	/**
	 * Determines if the server environment is compatible with Memberships.
	 *
	 * @since 1.11.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Returns the message for display when the environment is incompatible with Memberships.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		$message = sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );

		return $message;
	}


	/**
	 * Returns the main \WC_Memberships_Loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.11.0
	 *
	 * @return \WC_Memberships_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}

// fire it up!
WC_Memberships_Loader::instance();
