<?php
/**
 * Plugin Name: WooCommerce Cart Notices
 * Plugin URI: http://www.woocommerce.com/products/cart-notices/
 * Documentation URI: https://docs.woocommerce.com/document/woocommerce-cart-notices/
 * Description: Add dynamic notices above the cart and checkout to help increase your sales!
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 1.13.3
 * Text Domain: woocommerce-cart-notices
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Cart-Notices
 * @author    SkyVerge
 * @category  Plugin
 * @copyright Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 18706:cf25b8df1ffe2fa1880b076aa137f8d7
 * WC requires at least: 3.9.4
 * WC tested up to: 6.1.1
 */

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Cart Notices loader.
 *
 * @since 1.9.0
 */
class WC_Cart_Notices_Loader {


	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.0';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.2';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.9.4';

	/** SkyVerge plugin framework version used by this plugin */
	const FRAMEWORK_VERSION = '5.10.12';

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'WooCommerce Cart Notices';


	/** @var \WC_Cart_Notices_Loader single instance of this class */
	protected static $instance;

	/** @var array the admin notices to add */
	protected $notices = array();


	/**
	 * Constructs the class.
	 *
	 * @since 1.9.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init',    array( $this, 'check_environment' ) );
		add_action( 'admin_init',    array( $this, 'add_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_filter( 'extra_plugin_headers', array( $this, 'add_documentation_header') );

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {

			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.9.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.9.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.9.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.9.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.9.0
	 */
	public function init_plugin() {

		if ( $this->plugins_compatible() ) {

			$this->load_framework();

			// load the main plugin class
			require_once( plugin_dir_path( __FILE__ ) . 'class-wc-cart-notices.php' );

			// fire it up!
			wc_cart_notices();
		}
	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 1.9.0
	 */
	protected function load_framework() {

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Plugin' ) ) {

			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php' );
		}
	}


	/**
	 * Returns the framework version in namespace form.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function get_framework_version_namespace() {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Returns the framework version used by this plugin.
	 *
	 * @since 1.9.0
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
	 * @since 1.9.0
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
	 * @since 1.9.0
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
	 * @since 1.9.0
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
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.9.0
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
	 * @since 1.9.0
	 *
	 * @param string $slug the notice slug
	 * @param string $class the notice class
	 * @param string $message the notice message body
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}


	/**
	 * Displays any admin notices added with \SV_WC_Framework_Plugin_Loader::add_admin_notice()
	 *
	 * @internal
	 *
	 * @since 1.9.0
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
	 * @since 1.12.0
	 *
	 * @param string[] $headers original headers
	 * @return string[]
	 */
	public function add_documentation_header( $headers ) {

		$headers[] = 'Documentation URI';

		return $headers;
	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Returns the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
	}


	/**
	 * Returns the main plugin loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Cart_Notices_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}


// fire it up!
WC_Cart_Notices_Loader::instance();
