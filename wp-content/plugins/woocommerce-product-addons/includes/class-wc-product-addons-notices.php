<?php
/**
 * WC_PAO_Notices class
 *
 * @package  WooCommerce Product Add-ons
 * @since    6.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WC_PAO_Notices
 * @version  6.0.0
 */
class WC_PAO_Notices {

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

		self::$notice_options = get_option( 'wc_pao_notice_options', array() );

		// Save notice data.
		add_action( 'shutdown', array( __CLASS__, 'save_notice_options' ), 100 );
	}

	/**
	 * Get a setting for a notice type.
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
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	public static function set_notice_option( $notice_name, $key, $value ) {

		if ( ! is_scalar( $value ) && ! is_array( $value ) ) {
			return;
		}

		if ( ! is_string( $key ) ) {
			$key = strval( $key );
		}

		if ( ! is_string( $notice_name ) ) {
			$notice_name = strval( $notice_name );
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
			update_option( 'wc_pao_notice_options', self::$notice_options );
		}
	}
}

WC_PAO_Notices::init();
