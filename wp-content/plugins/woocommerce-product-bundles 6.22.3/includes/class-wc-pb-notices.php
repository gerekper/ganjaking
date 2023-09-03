<?php
/**
 * WC_PB_Notices class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WC_PB_Notices
 * @version  6.12.4
 */
class WC_PB_Notices {

	/**
	 * Feature plugin data by note name.
	 * @var array
	 */
	public static $plugin_data = array();

	/**
	 * Notice options.
	 * @var array
	 */
	public static $notice_options = array();

	/**
	 * Determines if notice options should be updated in the DB.
	 * @var boolean
	 */
	private static $should_update = false;

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$notice_options = get_option( 'wc_pb_notice_options', array() );

		self::$plugin_data = array(
			'wc-pb-bulk-discounts' => array(
				'install_path'  => 'product-bundles-bulk-discounts-for-woocommerce/product-bundles-bulk-discounts-for-woocommerce.php',
				'install_check' => 'WC_PB_Bulk_Discounts'
			)
		);

		// Save notice data.
		add_action( 'shutdown', array( __CLASS__, 'save_notice_options' ), 100 );
	}

	/**
	 * Get a setting for a notice type.
	 *
	 * @since  6.3.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function get_notice_option( $notice_name, $key, $default = null ) {
		return isset( self::$notice_options[ $notice_name ] ) && is_array( self::$notice_options[ $notice_name ] ) && isset( self::$notice_options[ $notice_name ][ $key ] ) ? self::$notice_options[ $notice_name ][ $key ] : $default;
	}

	/**
	 * Set a setting for a notice type.
	 *
	 * @since  6.3.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set_notice_option( $notice_name, $key, $value ) {

		if ( ! is_scalar( $value ) && ! is_array( $value ) ) {
			return;
		}

		if ( ! is_string( $key ) ) {
			$key = (string) $key;
		}

		if ( ! is_string( $notice_name ) ) {
			$notice_name = (string) $notice_name;
		}

		if ( ! isset( self::$notice_options ) || ! is_array( self::$notice_options ) ) {
			self::$notice_options = array();
		}

		if ( ! isset( self::$notice_options[ $notice_name ] ) || ! is_array( self::$notice_options[ $notice_name ] ) ) {
			self::$notice_options[ $notice_name ] = array();
		}

		self::$notice_options[ $notice_name ][ $key ] = $value;
		self::$should_update                          = true;
	}

	/**
	 * Save notice options to the DB.
	 */
	public static function save_notice_options() {
		if ( self::$should_update ) {
			update_option( 'wc_pb_notice_options', self::$notice_options );
		}
	}

	/**
	 * Used to determine if a feature plugin is installed.
	 *
	 * @param  string  $name
	 * @return boolean|null
	 */
	public static function is_feature_plugin_installed( $name ) {

		if ( ! isset( self::$plugin_data[ $name ] ) ) {
			return null;
		}

		if ( class_exists( self::$plugin_data[ $name ][ 'install_check' ] ) ) {
			return true;
		}

		include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		return 0 === validate_plugin( self::$plugin_data[ $name ][ 'install_path' ] );
	}
}

WC_PB_Notices::init();
