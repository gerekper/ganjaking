<?php
/**
 * WooCommerce Account Funds requirements
 *
 * @package WC_Account_Funds
 * @since   2.2.0
 */

/**
 * Class WC_Account_Funds_Requirements
 */
class WC_Account_Funds_Requirements {

	/**
	 * Minimum PHP version required.
	 */
	const MINIMUM_PHP_VERSION = '5.6';

	/**
	 * Minimum WordPress version required.
	 */
	const MINIMUM_WP_VERSION = '4.9';

	/**
	 * Minimum WooCommerce version required.
	 */
	const MINIMUM_WC_VERSION = '3.7';

	/**
	 * Requirements errors.
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Init.
	 *
	 * @since 2.2.0
	 */
	public static function init() {
		self::check_requirements();

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	/**
	 * Checks the plugin requirements.
	 *
	 * @since 2.2.0
	 */
	protected static function check_requirements() {
		if ( ! self::is_php_compatible() ) {
			self::$errors[] = sprintf(
				/* translators: 1: Minimum PHP version 2: Current PHP version */
				_x( '<strong>WooCommerce Account Funds</strong> requires PHP %1$s or higher. You are using version %2$s', 'admin notice', 'woocommerce-account-funds' ),
				self::MINIMUM_PHP_VERSION,
				PHP_VERSION
			);
		} elseif ( ! self::is_wp_compatible() ) {
			self::$errors[] = sprintf(
				/* translators: 1: Minimum WordPress version 2: Current WordPress version */
				_x( '<strong>WooCommerce Account Funds</strong> requires WordPress %1$s or higher. You are using version %2$s', 'admin notice', 'woocommerce-account-funds' ),
				self::MINIMUM_WP_VERSION,
				get_bloginfo( 'version' )
			);
		} elseif ( ! is_woocommerce_active() ) {
			self::$errors[] = _x( '<strong>WooCommerce Account Funds</strong> requires WooCommerce to be activated to work.', 'admin notice', 'woocommerce-account-funds' );
		} elseif ( ! self::is_wc_compatible() ) {
			self::$errors[] = sprintf(
				/* translators: 1: Minimum WooCommerce version 2: Current WooCommerce version */
				_x( '<strong>WooCommerce Account Funds</strong> requires WooCommerce %1$s or higher. You are using version %2$s', 'admin notice', 'woocommerce-account-funds' ),
				self::MINIMUM_WC_VERSION,
				get_option( 'woocommerce_db_version' )
			);
		}
	}

	/**
	 * Gets if the minimum PHP version requirement is satisfied.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_php_compatible() {
		return ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' ) );
	}

	/**
	 * Gets if the minimum WordPress version requirement is satisfied.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_wp_compatible() {
		return ( version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' ) );
	}

	/**
	 * Gets if the minimum WooCommerce version requirement is satisfied.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_wc_compatible() {
		return ( version_compare( get_option( 'woocommerce_db_version' ), self::MINIMUM_WC_VERSION, '>=' ) );
	}

	/**
	 * Outputs the plugin requirements errors.
	 *
	 * @since 2.2.0
	 */
	public static function admin_notices() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		foreach ( self::$errors as $error ) {
			printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $error ) );
		}
	}

	/**
	 * Gets if the plugin requirements are satisfied.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function are_satisfied() {
		return empty( self::$errors );
	}
}

WC_Account_Funds_Requirements::init();
