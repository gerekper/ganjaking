<?php
/**
 * Class to add compatibility with the WooCommerce Subscriptions extension.
 *
 * @package WC_OD
 * @since  2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Subscriptions_Data_Copier
 */
class WC_OD_Subscriptions_Data_Copier {

	/**
	 * Init.
	 *
	 * @since 2.5.0
	 */
	public static function init() {
		if ( version_compare( WC_Subscriptions::$version, '4.7.0', '>=' ) ) {
			add_filter( 'wc_subscriptions_object_data', array( __CLASS__, 'copy_data' ), 10, 4 );
		} else {
			add_filter( 'wcs_subscription_meta', array( __CLASS__, 'copy_meta_filter' ), 10, 3 );
			add_filter( 'wcs_renewal_order_meta', array( __CLASS__, 'copy_meta_filter' ), 10, 3 );
			add_filter( 'wcs_resubscribe_order_meta', array( __CLASS__, 'copy_meta_filter' ), 10, 3 );
		}
	}

	/**
	 * Copies the delivery data from one object to another.
	 *
	 * @since 2.5.0
	 *
	 * @param array    $data {
	 *     The data to be copied to the "to" object. Each value is keyed by the meta key. Example format [ '_meta_key' => 'meta_value' ].
	 *
	 *     @type mixed $meta_value The meta value to be copied.
	 * }
	 * @param WC_Order $to_object   The object to copy data to.
	 * @param WC_Order $from_object The object to copy data from.
	 * @param string   $type        The copy type.
	 * @return array
	 */
	public static function copy_data( $data, $to_object, $from_object, $type ) {
		$exclude_data = self::get_data_to_exclude( $to_object, $from_object, $type );

		if ( ! empty( $exclude_data ) ) {
			$data = array_diff_key( $data, array_flip( $exclude_data ) );
		}

		return $data;
	}

	/**
	 * Filters the metadata that will be copied from a subscription to an order and vice-versa.
	 *
	 * Backward compatibility for WC Subscriptions versions lower than 4.7.
	 *
	 * @since 2.5.0
	 *
	 * @param array    $meta        The metadata to be copied.
	 * @param WC_Order $to_object   The object to copy data to.
	 * @param WC_Order $from_object The object to copy data from.
	 * @return array An array with the order metadata.
	 */
	public static function copy_meta_filter( $meta, $to_object, $from_object ) {
		$type = str_replace( array( 'wcs_', '_meta' ), '', current_filter() );

		return self::copy_meta( $meta, $to_object, $from_object, $type );
	}

	/**
	 * Copies the delivery metadata from one object to another.
	 *
	 * Backward compatibility for WC Subscriptions versions lower than 4.7.
	 *
	 * @since 2.5.0
	 *
	 * @param array    $meta        The metadata to be copied.
	 * @param WC_Order $to_object   The object to copy data to.
	 * @param WC_Order $from_object The object to copy data from.
	 * @param string   $type        The copy type.
	 * @return array
	 */
	public static function copy_meta( $meta, $to_object, $from_object, $type ) {
		$exclude_metas = self::get_data_to_exclude( $to_object, $from_object, $type );

		if ( empty( $exclude_metas ) ) {
			return $meta;
		}

		$meta_keys = wp_list_pluck( $meta, 'meta_key' );

		// Exclude the meta keys from the copy.
		foreach ( $exclude_metas as $exclude_meta ) {
			$index = array_search( $exclude_meta, $meta_keys, true );

			if ( false !== $index ) {
				unset( $meta[ $index ] );
			}
		}

		return $meta;
	}

	/**
	 * Gets the metadata to exclude from the data copy.
	 *
	 * @since 2.5.0
	 *
	 * @param WC_Order $to_object   The object to copy data to.
	 * @param WC_Order $from_object The object to copy data from.
	 * @param string   $type        The copy type.
	 * @return array
	 */
	protected static function get_data_to_exclude( $to_object, $from_object, $type ) {
		$meta = array();

		if ( 'parent' !== $type ) {
			$meta[] = '_delivery_days';
			$meta[] = '_shipping_date';
		}

		// Use the checkout form fields values.
		if ( 'renewal_order' === $type && wcs_cart_contains_renewal() ) {
			$meta[] = '_delivery_date';
			$meta[] = '_delivery_time_frame';
		}

		/**
		 * Filters the metadata to exclude from the data copy.
		 *
		 * @since 1.5.0
		 * @since 1.5.5 Added `$type` parameter.
		 *
		 * @param array    $meta        The meta keys to exclude.
		 * @param WC_Order $to_object   The object to copy data to.
		 * @param WC_Order $from_object The object to copy data from.
		 * @param string   $type        The copy type.
		 */
		return apply_filters( 'wc_od_exclude_order_meta', $meta, $to_object, $from_object, $type );
	}
}

WC_OD_Subscriptions_Data_Copier::init();
