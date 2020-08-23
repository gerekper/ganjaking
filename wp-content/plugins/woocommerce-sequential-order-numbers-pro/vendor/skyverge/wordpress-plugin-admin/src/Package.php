<?php
/**
 * WordPress Admin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WordPress\Plugin_Admin;

defined( 'ABSPATH' ) or exit;

/**
 * The base package class.
 *
 * @since 1.0.0
 */
class Package {

	/** @var string the package ID */
	const ID = 'wordpress-plugin-admin';

	/** @var string the package version */
	const VERSION = '1.0.1';

	/** @var string the minimum required version of WooCommerce */
	const MINIMUM_WOOCOMMERCE_VERSION = '3.5';


	/** @var Package single instance of this package */
	private static $instance;

	/** @var Menus menus handler instance */
	private $menus;

	/** @var Dashboard Dashboard page handler instance */
	private $dashboard;

	/** @var Support Support (Get Help) page handler instance */
	private $support;

	/** @var REST_API REST API handler instance */
	private $rest_api;


	/**
	 * Package constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// bail if WooCommerce is not active or compatible
		// TODO: as non-WooCommerce features are added to this package, consider more granular control over the components that get loaded when WooCommerce isn't active {2020-07-07}
		if ( ! self::is_woocommerce_compatible() ) {
			return;
		}

		require_once( self::get_package_path() . '/Menus.php' );
		require_once( self::get_package_path() . '/Page.php' );
		require_once( self::get_package_path() . '/Dashboard.php' );
		require_once( self::get_package_path() . '/Support.php' );
		require_once( self::get_package_path() . '/REST_API.php' );

		$this->menus     = new Menus();
		$this->dashboard = new Dashboard();
		$this->support   = new Support();
		$this->rest_api  = new REST_API();

		$this->add_hooks();
	}


	/**
	 * Adds the necessary action and filter hooks.
	 *
	 * @since 1.0.0
	 */
	private function add_hooks() {

		// load the translation files
		add_action( 'init', array( $this, 'load_translations' ) );
	}


	/**
	 * Loads the translation files.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function load_translations() {

		// user's locale if in the admin for WP 4.7+, or the site locale otherwise
		$locale = is_admin() && is_callable( 'get_user_locale' ) ? get_user_locale() : get_locale();

		$locale = apply_filters( 'plugin_locale', $locale, self::ID );

		load_textdomain( self::ID, WP_LANG_DIR . '/sv-wordpress-plugin-admin/sv-wordpress-plugin-admin-' . $locale . '.mo' );

		load_plugin_textdomain( self::ID, false, untrailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . '/i18n/languages' );
	}


	/**
	 * Gets the menus handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Menus
	 */
	public function get_menus_handler() {

		return $this->menus;
	}


	/**
	 * Gets the Dashboard page handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Dashboard
	 */
	public function get_dashboard_handler() {

		return $this->dashboard;
	}


	/**
	 * Gets the Support (Get Help) page handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Support
	 */
	public function get_support_handler() {

		return $this->support;
	}


	/**
	 * Gets the REST API handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return REST_API
	 */
	public function get_rest_api_handler() {

		return $this->rest_api;
	}


	/**
	 * Gets the package path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_package_path() {

		return untrailingslashit( __DIR__ );
	}


	/**
	 * Gets the package URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_package_url() {

		return untrailingslashit( plugins_url( '', __FILE__ ) );
	}


	/** Conditional methods *******************************************************************************************/


	/**
	 * Determines if WooCommerce is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {

		if ( ! did_action( 'plugins_loaded' ) ) {
			_doing_it_wrong( __METHOD__, 'Cannot be called before plugins_loaded is fired', '1.0.0' );
		}

		return function_exists( 'WC' );
	}


	/**
	 * Determines if the current version WooCommerce is compatible with this package.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_woocommerce_compatible() {

		return self::is_woocommerce_active() && defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WOOCOMMERCE_VERSION, '>=' );
	}


	/**
	 * Determines whether the current admin screen is a SkyVerge admin screen (any screen under the SkyVerge top-level menu item).
	 *
	 * If no specific ID is passed, we check for any of them.
	 *
	 * @since 1.0.0
	 *
	 * @param string $screen_id screen ID
	 * @return bool
	 */
	public function is_skyverge_admin_screen( $screen_id = '' ) {
		global $current_screen;

		$is_screen = false;

		if ( ! empty( $screen_id ) ) {

			$screen_ids = [ $screen_id ];

		} else {

			$screen_ids = [
				Dashboard::SCREEN_ID,
			];
		}

		foreach ( $screen_ids as $screen_id ) {

			if ( isset( $current_screen->id ) && $screen_id === $current_screen->id ) {

				$is_screen = true;
				break;
			}
		}

		return $is_screen;
	}


	/** Utility methods ***********************************************************************************************/


	/**
	 * Gets the one true instance of Package.
	 *
	 * @since 1.0.0
	 *
	 * @return Package
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}
