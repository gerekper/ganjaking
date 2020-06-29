<?php
/**
 * WooCommerce Customer/Order XML Export Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * The unified plugin installer.
 *
 * @since 2.6.3
 */
class WC_Customer_Order_XML_Export_Suite_Unified_Installer {

	/** @var string Woo product ID for the unified plugin */
	const PRODUCT_ID = '18652';

	/** @var string sales page URL */
	const SALES_PAGE_URL = 'https://woocommerce.com/products/ordercustomer-csv-export/';

	/** @var string minimum PHP version required by the unified plugin */
	const MINIMUM_PHP = '5.6';

	/** @var string minimum WC version required by the unified plugin */
	const MINIMUM_WC = '3.0';


	/**
	 * Installs and activates the unified Export plugin.
	 *
	 * @since 2.6.3
	 *
	 * @param string $redirect_url URL to redirect after activation
	 * @throws \Exception
	 */
	public static function install_and_activate( $redirect_url ) {

		// install if not already
		if ( ! self::is_installed() ) {
			self::install();
		}

		self::activate( $redirect_url );
	}


	/**
	 * Installs the unified Export plugin.
	 *
	 * @since 2.6.3
	 *
	 * @throws \Exception
	 */
	public static function install() {

		// security check
		if ( ! current_user_can( 'install_plugins' ) ) {
			throw new \Exception( 'You are not allowed to install plugins' );
		}

		if ( ! self::is_php_compatible() ) {
			throw new \Exception( 'Requires PHP version ' . self::MINIMUM_PHP . ' or higher' );
		}

		if ( ! self::is_wc_compatible() ) {
			throw new \Exception( 'Requires WooCommerce version ' . self::MINIMUM_WC . ' or higher' );
		}

		// flush caches
		delete_transient( '_woocommerce_helper_subscriptions' );
		\WC_Helper_Updater::flush_updates_cache();

		if ( ! self::is_connected() ) {
			throw new \Exception( 'Woo helper is not connected' );
		}

		if ( ! self::has_subscription() ) {
			throw new \Exception( 'No subscription found' );
		}

		$updates = \WC_Helper_Updater::get_update_data();

		if ( empty( $updates[ self::PRODUCT_ID ]['package'] ) ) {
			throw new \Exception( 'Package not found' );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		WP_Filesystem();

		$skin     = new \Automatic_Upgrader_Skin();
		$upgrader = new \WP_Upgrader( $skin );

		$download = $upgrader->download_package( $updates[ self::PRODUCT_ID ]['package'] );

		if ( is_wp_error( $download ) ) {
			throw new \Exception( $download->get_error_message() );
		}

		$working_dir = $upgrader->unpack_package( $download, true );

		if ( is_wp_error( $working_dir ) ) {
			throw new \Exception( $working_dir->get_error_message() );
		}

		$result = $upgrader->install_package(
			[
				'source'        => $working_dir,
				'destination'   => WP_PLUGIN_DIR,
				'clear_working' => true,
				'hook_extra'    => [
					'type'   => 'plugin',
					'action' => 'install',
				],
			]
		);

		if ( is_wp_error( $result ) ) {
			throw new \Exception( $result->get_error_message() );
		}

		wp_clean_plugins_cache();
	}


	/**
	 * Activates the unified plugin.
	 *
	 * @since 2.6.3
	 *
	 * @param string $redirect_url URL to redirect after activation
	 * @throws \Exception
	 */
	public static function activate( $redirect_url ) {

		// security check
		if ( ! current_user_can( 'activate_plugins' ) ) {
			throw new \Exception( 'You are not allowed to activate plugins' );
		}

		update_option( 'wc_customer_order_export_migrate_from_xml', 'yes' );

		$result = activate_plugin( self::get_plugin_file(), $redirect_url );

		if ( $result && is_wp_error( $result ) ) {
			throw new \Exception( $result->get_error_message() );
		}
	}


	/** Conditional methods *******************************************************************************************/


	/**
	 * Determines if the site has a subscription for the unified plugin.
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function has_subscription() {

		$subscriptions = wp_list_filter( \WC_Helper::get_subscriptions(), [ 'product_id' => self::PRODUCT_ID ] );

		return ! empty( $subscriptions );
	}


	/**
	 * Determines if the site is connected to WooCommerce.com
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function is_connected() {

		$auth = \WC_Helper_Options::get( 'auth' );

		return ! empty( $auth['access_token'] );
	}


	/**
	 * Determines if unified plugin is already installed.
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function is_installed() {

		return ( self::get_plugin_file() );
	}


	/**
	 * Determines if the new plugin is supported by the current environment (PHP & WC versions).
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function is_supported() {

		return self::is_php_compatible() && self::is_wc_compatible();
	}


	/**
	 * Determines if the current version of PHP is supported by the new plugin.
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function is_php_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP, '>=' );
	}


	/**
	 * Determines if the current version of WooCommerce is supported by the new plugin.
	 *
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	public static function is_wc_compatible() {

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC, '>=' );
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the installation prompt message.
	 *
	 * Crafts a message depending on whether the site is connected to WooCommerce & has an active subscription for
	 * the unified plugin.
	 *
	 * @since 2.6.3
	 *
	 * @param string $plugin_name existing plugin name, for display
	 * @param string $install_url URL that triggers installation
	 * @return string
	 */
	public static function get_prompt_message( $plugin_name, $install_url ) {

		$preamble = sprintf(
		/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - plugin name */
			__( 'Hey there, %1$syour action is needed%2$s: %3$s has been replaced by the unified Customer/Order/Coupon Export plugin, which now supports exporting in both CSV and XML.', 'woocommerce-customer-order-xml-export-suite' ),
			'<strong>', '</strong>',
			$plugin_name
		);

		$message = '';

		if ( self::is_supported() ) {

			if ( self::is_connected() ) {

				if ( self::has_subscription() ) {

					$message = sprintf(
					/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag  */
						__( '%1$sClick here to install and activate the new plugin%2$s. Your settings and automated exports will be migrated automatically.', 'woocommerce-customer-order-xml-export-suite' ),
						'<a href="' . esc_url( $install_url ) . '">', '</a>'
					);
				}

			} else {

				$message = sprintf(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag  */
					__( '%1$sConnect your store%2$s to WooCommerce.com to download the new plugin.', 'woocommerce-customer-order-xml-export-suite' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=wc-addons&section=helper' ) ) . '">', '</a>'
				);
			}
		}

		$addendum = sprintf(
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag  */
			__( 'If you\'d like to learn more about the updated plugin, please %1$sread more here%2$s.', 'woocommerce-customer-order-xml-export-suite' ),
			'<a href="https://docs.woocommerce.com/document/ordercustomer-csv-export/" target="_blank">', '</a>'
		);

		if ( $message ) {
			$message = "{$preamble} {$message}</br></br>{$addendum}";
		} else {
			$message = "{$preamble} {$addendum}";
		}

		return $message;
	}


	/**
	 * Gets the unified plugin file name.
	 *
	 * @since 2.9.2-dev.1
	 *
	 * @return string
	 */
	private static function get_plugin_file() {

		$plugins = self::get_local_plugin_data();

		return is_array( $plugins ) && ! empty( $plugins ) ? key( $plugins ) : '';
	}


	/**
	 * Gets the local WooCommerce plugin data.
	 *
	 * @since 2.9.2-dev.1
	 *
	 * @return array
	 */
	private static function get_local_plugin_data() {

		return wp_list_filter( \WC_Helper::get_local_woo_plugins(), [ '_product_id' => self::PRODUCT_ID ] );
	}


}
