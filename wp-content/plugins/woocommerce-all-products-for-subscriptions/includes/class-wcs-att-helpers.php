<?php
/**
 * WCS_ATT_Helpers class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @package  WooCommerce All Products For Subscriptions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Helper Functions.
 *
 * @class    WCS_ATT_Helpers
 * @version  2.3.0
 */
class WCS_ATT_Helpers {

	/**
	 * Runtime cache for simple storage.
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

	/**
	 * True when processing a FE request.
	 *
	 * @return boolean
	 */
	public static function is_front_end() {
		$is_fe = ( ! is_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		return $is_fe;
	}
}
