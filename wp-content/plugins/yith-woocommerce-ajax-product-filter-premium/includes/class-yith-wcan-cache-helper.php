<?php
/**
 * Cache class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.1.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Cache_Helper' ) ) {
	/**
	 * Cache class.
	 * This class manage all the cache functionalities.
	 *
	 * @since 4.1.2
	 */
	class YITH_WCAN_Cache_Helper {
		/**
		 * Array of available transients
		 *
		 * @var array
		 */
		protected static $transients = array();

		/**
		 * Array of transients that needs to be saved on shutdown
		 *
		 * @var array
		 */
		protected static $need_update = array();

		/**
		 * Bootstrap the cache
		 */
		public static function init() {
			// init transients array.
			self::init_transients();

			// register transients for update.
			add_action( 'shutdown', array( __CLASS__, 'update_transients' ), 10 );
		}

		/**
		 * Returns a specific value in a transient
		 *
		 * @param string $transient Transient name.
		 * @param string $index     Optional transient's index to return (assumes transient is an array).
		 *
		 * @return mixed Value of the transient (or value of the specific index inside transient).
		 */
		public static function get( $transient, $index = null ) {
			if ( apply_filters( 'yith_wcan_suppress_cache', false ) ) {
				return false;
			}

			if ( ! isset( self::$transients[ $transient ] ) ) {
				return false;
			}

			if ( ! isset( self::$transients[ $transient ]['value'] ) ) {
				self::$transients[ $transient ]['value'] = get_transient( self::$transients[ $transient ]['name'] );
			}

			if ( is_null( $index ) ) {
				return self::$transients[ $transient ]['value'];
			}

			if ( ! isset( self::$transients[ $transient ]['value'][ $index ] ) ) {
				return false;
			}

			return apply_filters( 'yith_wcan_get_transient', self::$transients[ $transient ]['value'][ $index ], $transient, $index );
		}

		/**
		 *  Sets a specific value in a transient
		 *
		 * @param string $transient Transient name.
		 * @param mixed  $value     Value to set; could be the entire transient value, or value of a specific index of the transient, assuming it is an array.
		 * @param string $index     Optional transient's index to set (assumes transient is an array).
		 * @param bool   $now       Whether to save transient immediately or allow system to do it at shutdown.
		 *
		 * @return bool|mixed False on failure, new value otherwise.
		 */
		public static function set( $transient, $value, $index = null, $now = false ) {
			if ( apply_filters( 'yith_wcan_suppress_cache', false ) ) {
				return false;
			}

			if ( ! isset( self::$transients[ $transient ] ) ) {
				return false;
			}

			if ( is_null( $index ) ) {
				self::$transients[ $transient ]['value'] = $value;
			} else {
				if ( ! isset( self::$transients[ $transient ]['value'] ) ) {
					self::$transients[ $transient ]['value'] = array();
				}
				self::$transients[ $transient ]['value'][ $index ] = $value;
			}

			if ( $now ) {
				set_transient( self::$transients[ $transient ]['name'], self::$transients[ $transient ]['value'], self::$transients[ $transient ]['duration'] );
			} else {
				self::mark_for_update( $transient );
			}

			return $value;
		}

		/**
		 * Completely deletes a transient.
		 *
		 * @param string $transient Transient name.
		 * @param bool   $now       Whether to save transient immediately or allow system to do it at shutdown.
		 *
		 * @return bool False on failure; true otherwise.
		 */
		public static function delete( $transient, $now = false ) {
			if ( apply_filters( 'yith_wcan_suppress_cache', false ) ) {
				return false;
			}

			if ( ! isset( self::$transients[ $transient ] ) ) {
				return false;
			}

			if ( $now ) {
				delete_transient( self::$transients[ $transient ]['name'] );
			} else {
				self::$transients[ $transient ]['value'] = false;
				self::mark_for_update( $transient );
			}

			return true;
		}

		/**
		 * Update transients at shutdown
		 *
		 * @return void
		 */
		public static function update_transients() {
			if ( apply_filters( 'yith_wcan_suppress_cache', false ) ) {
				return;
			}

			if ( empty( self::$need_update ) ) {
				return;
			}

			foreach ( self::$need_update as $transient ) {
				if ( ! isset( self::$transients[ $transient ] ) ) {
					continue;
				}

				$value = self::$transients[ $transient ]['value'];

				if ( ! $value ) {
					delete_transient( self::$transients[ $transient ]['name'] );
				} else {
					set_transient( self::$transients[ $transient ]['name'], $value, self::$transients[ $transient ]['duration'] );
				}
			}

			self::$need_update = array();
		}

		/**
		 * Deletes all plugin transients
		 *
		 * @return void
		 */
		public static function delete_transients() {
			// delete current version of the transients.
			foreach ( self::$transients as $transient_options ) {
				delete_transient( $transient_options['name'] );
			}

			// delete past versions of the transients.
			self::delete_expired_transients();

			delete_transient( 'yith_wcan_exclude_from_catalog_product_ids' );
		}

		/**
		 * Deletes old version of the transients still in memory
		 *
		 * @return void
		 */
		public static function delete_expired_transients() {
			global $wpdb;

			$cache_version = WC_Cache_Helper::get_transient_version( 'product' );
			$to_delete     = array();

			foreach ( self::$transients as $transient_options ) {
				$to_delete[] = str_replace( $cache_version, '%', $transient_options['name'] );
			}

			$query = "DELETE FROM {$wpdb->options} WHERE 1=1";
			$args  = array();

			$query .= ' AND ( ';
			$first  = true;
			foreach ( $to_delete as $transient_name ) {
				if ( ! $first ) {
					$query .= ' OR ';
				}

				$args[] = "%{$transient_name}%";

				$query .= 'option_name LIKE %s';
				$first  = false;
			}
			$query .= ')';

			$query .= ' AND option_name NOT LIKE %s';
			$args[] = "%{$cache_version}%";

			$wpdb->query( $wpdb->prepare( $query, $args ) ); // phpcs:ignore WordPress.DB
		}

		/**
		 * Init supported transients
		 *
		 * @return void
		 */
		protected static function init_transients() {
			$cache_version    = WC_Cache_Helper::get_transient_version( 'product' );
			$language_postfix = self::get_language_postfix();

			$transient_array    = array();
			$builtin_transients = array(
				'queried_products',
				'object_in_terms',
				'products_instock',
			);

			foreach ( $builtin_transients as $transient ) {
				$default_name = "yith_wcan_{$transient}_{$cache_version}{$language_postfix}";

				$transient_array[ $transient ] = array(
					'name'     => apply_filters( "yith_wcan_{$transient}_name", $default_name ),
					'duration' => apply_filters( "yith_wcan_{$transient}_duration", 30 * DAY_IN_SECONDS ),
				);
			}

			// add legacy transients.
			$transient_array['exclude_from_catalog_product_ids'] = array(
				'name'     => 'yith_wcan_exclude_from_catalog_product_ids',
				'duration' => 30 * DAY_IN_SECONDS,
			);

			self::$transients = apply_filters( 'yith_wcan_cache_helper_transients', $transient_array );
		}

		/**
		 * Return postfix to be used in transient names, if a multi-language plugin is installed
		 *
		 * TODO: remove this method, and filter transient names in WPML compatibility class
		 *
		 * @return string
		 */
		protected static function get_language_postfix() {
			$postfix      = '';
			$current_lang = apply_filters( 'wpml_current_language', null );

			if ( ! empty( $current_lang ) ) {
				$postfix = "_{$current_lang}";
			}

			return $postfix;
		}

		/**
		 * Set a transient as requiring update
		 *
		 * @param string $transient Transient name.
		 *
		 * @eturn void
		 */
		protected static function mark_for_update( $transient ) {
			if ( in_array( $transient, self::$need_update, true ) ) {
				return;
			}

			self::$need_update[] = $transient;
		}
	}
}

YITH_WCAN_Cache_Helper::init();
