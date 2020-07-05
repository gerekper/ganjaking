<?php
/**
 * WC_CP_Notices class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    7.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WC_CP_Notices
 * @version  7.0.4
 */
class WC_CP_Notices {

	/**
	 * Notice options.
	 * @var array
	 */
	public static $notice_options = array();

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$notice_options = get_option( 'wc_cp_notice_options', array() );

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

		if ( ! isset( self::$notice_options ) ) {
			self::$notice_options = array();
		}

		if ( ! isset( self::$notice_options[ $notice_name ] ) ) {
			self::$notice_options[ $notice_name ] = array();
		}

		self::$notice_options[ $notice_name ][ $key ] = $value;
	}

	/**
	 * Save notice options to the DB.
	 */
	public static function save_notice_options() {
		update_option( 'wc_cp_notice_options', self::$notice_options );
	}
}

WC_CP_Notices::init();
