<?php
/**
 * WC_CSP_Helpers class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper Functions.
 *
 * @class    WC_CSP_Helpers
 * @version  1.8.7
 */
class WC_CSP_Helpers {

	/**
	 * Runtime cache.
	 *
	 * @var array
	 */
	public static $cache = array();

	/**
	 * Simple runtime cache getter.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @return mixed
	 */
	public static function cache_get( $key, $group_key = '' ) {

		$value = null;

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				$value = self::cache_get( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			$value = self::$cache[ $key ];
		}

		return $value;
	}

	/**
	 * Simple runtime cache setter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_set( $key, $value, $group_key = '' ) {

		if ( $group_key ) {

			if ( null === ( $group_id = self::cache_get( $group_key . '_id' ) ) ) {
				$group_id = md5( $group_key );
				self::cache_set( $group_key . '_id', $group_id );
			}

			self::$cache[ $group_key . '_' . $group_id . '_' . $key ] = $value;

		} else {
			self::$cache[ $key ] = $value;
		}
	}

	/**
	 * Simple runtime cache unsetter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_delete( $key, $group_key = '' ) {

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				self::cache_delete( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			unset( self::$cache[ $key ] );
		}
	}

	/**
	 * Simple runtime group cache invalidator.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function cache_invalidate( $group_key ) {

		if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
			$group_id = md5( $group_key . '_' . $group_id );
			self::cache_set( $group_key . '_id', $group_id );
		}
	}
}
