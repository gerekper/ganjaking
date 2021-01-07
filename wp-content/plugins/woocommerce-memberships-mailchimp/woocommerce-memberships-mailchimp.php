<?php
/**
 * Plugin Name: MailChimp for WooCommerce Memberships
 * Plugin URI: https://www.woocommerce.com/products/mailchimp-woocommerce-memberships/
 * Documentation URI: https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/
 * Description: Sync your member lists to MailChimp for improved email segmentation
 * Author: SkyVerge
 * Author URI: https://www.woocommerce.com/
 * Version: 1.4.0
 * Text Domain: woocommerce-memberships-mailchimp
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2017-2019 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   SkyVerge\WooCommerce\Memberships\MailChimp
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 3007049:6046684d2432e8520e56028a64de70be
 * WC requires at least: 3.5
 * WC tested up to: 4.7.1
 */

defined( 'ABSPATH' ) or exit;

// ensure required functions are loaded
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// queue plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '6046684d2432e8520e56028a64de70be', '3007049' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

/**
 * The plugin loader class.
 *
 * @since 1.0.0
 */
class WC_Memberships_MailChimp_Sync_Loader {


	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.0';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.2';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.5';

	/** minimum Memberships version required by this plugin */
	const MIN_MEMBERSHIPS_VERSION = '1.9.0';

	/** SkyVerge plugin framework version used by this plugin */
	const FRAMEWORK_VERSION = '5.5.0';

	/** plugin namespace */
	const PLUGIN_NAMESPACE = 'SkyVerge\WooCommerce\Memberships\MailChimp';

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'MailChimp for WooCommerce Memberships';

	/** @var \WC_Memberships_MailChimp_Sync_Loader single instance of this plugin */
	protected static $instance;

	/** @var array the admin notices to add */
	private $notices = array();


	/**
	 * Initializes the loader.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_filter( 'extra_plugin_headers', array( $this, 'add_documentation_header' ) );

		// if the environment checks pass, initialize the plugin
		if ( $this->is_environment_compatible() ) {

			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {

		if ( ! $this->plugins_compatible() || ! self::is_woocommerce_active() || ! self::is_memberships_active() ) {
			return;
		}

		$this->load_framework();

		// autoload plugin and vendor files
		$loader = require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

		// register plugin namespace with autoloader
		$loader->addPsr4( self::PLUGIN_NAMESPACE . '\\', __DIR__ . '/src' );

		// include the functions file to make wc_memberships_mailchimp() available
		require_once( plugin_dir_path( __FILE__ ) . '/src/Functions/Functions.php' );

		wc_memberships_mailchimp();
	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.6
	 *
	 * @return string
	 */
	protected function get_framework_version_namespace() {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Returns the framework version used by this plugin.
	 *
	 * @since 1.0.6
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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

		if ( ! self::is_woocommerce_active() ) {

			$this->add_admin_notice( 'install_woocommerce', 'error', sprintf(
				'%s requires WooCommerce to function. Please %sinstall WooCommerce &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				'<a href="' . esc_url( admin_url( 'plugin-install.php' ) ) . '">', '</a>'
			) );

		} elseif ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%s requires WooCommerce version %s or higher. Please %supdate WooCommerce &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! self::is_memberships_active() ) {

			$this->add_admin_notice( 'install_memberships', 'error', sprintf(
				'%s requires WooCommerce Memberships to function. Please %sinstall WooCommerce Memberships &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				'<a href="https://woocommerce.com/products/woocommerce-memberships/">', '</a>'
			) );

		} elseif ( ! $this->is_memberships_compatible() ) {

			$this->add_admin_notice( 'update_memberships', 'error', sprintf(
				'%s requires WooCommerce Memberships version %s or higher. Please %supdate WooCommerce Memberships &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MIN_MEMBERSHIPS_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}
	}


	/**
	 * Checks if a plugin is active.
	 *
	 * @see SV_WC_Plugin::is_plugin_active() but we can't use that yet, so the code here partially duplicates it
	 *
	 * @since 1.0.7
	 *
	 * @param string $plugin plugin filename with full path
	 * @return bool
	 */
	private static function is_plugin_active( $plugin ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin, $active_plugins, false ) || array_key_exists( $plugin, $active_plugins );
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		if ( ! self::MINIMUM_WP_VERSION ) {
			$is_compatible = true;
		} else {
			$is_compatible = version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
		}

		return $is_compatible;
	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		if ( ! self::MINIMUM_WC_VERSION ) {
			$is_compatible = true;
		} else {
			$is_compatible = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
		}

		return $is_compatible;
	}


	/**
	 * Determines if WooCommerce is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {

		return self::is_plugin_active( 'woocommerce/woocommerce.php' );
	}


	/**
	 * Determines if Memberships is compatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_memberships_compatible() {

		return self::is_memberships_active() && version_compare( self::get_memberships_version(), self::MIN_MEMBERSHIPS_VERSION, '>=' );
	}


	/**
	 * Determines if Memberships is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_memberships_active() {

		return get_option( 'wc_memberships_is_active', false ) && self::is_plugin_active( 'woocommerce-memberships/woocommerce-memberships.php' );
	}


	/**
	 * Returns the installed Memberships version number.
	 *
	 * Unfortunately we can't use `wc_memberships()->get_version()` as it's too early to access that.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_memberships_version() {

		return get_option( 'wc_memberships_version', '0' );
	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
     *
     * @param string $slug the notice slug
     * @param string $class the notice class
     * @param string $message the notice message
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}


	/**
	 * Displays admin notices added with \WC_Memberships_MailChimp_Sync_Loader::add_admin_notice()
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {

		foreach ( $this->notices as $notice_key => $notice ) :

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
	 * @since 1.3.0
	 *
	 * @param string[] $headers original headers
	 * @return string[]
	 */
	public function add_documentation_header( $headers ) {

		$headers[] = 'Documentation URI';

		return $headers;
	}


	/**
	 * Returns the main \WC_Memberships_MailChimp_Sync_Loader.
	 *
	 * Ensures only one instance is loaded at one time.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Memberships_MailChimp_Sync_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

// fire it up!
WC_Memberships_MailChimp_Sync_Loader::instance();
