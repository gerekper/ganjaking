<?php
/**
 * REST API Reports data store.
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;

/**
 * WC_PB_REST_Reports_Bundles_Data_Store class.
 *
 * @version 6.9.0
 */
abstract class WC_PB_Analytics_Data_Store extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Wrapper around DataStore::get_cached_data().
	 *
	 * @param string $cache_key Cache key.
	 * @return mixed
	 */
	protected function get_cached_data( $cache_key ) {

		$using_object_cache = wp_using_ext_object_cache();
		$transient_version  = WC_Cache_Helper::get_transient_version( 'woocommerce_' . $this->cache_key . '_reports' ) . '_' . ReportsCache::get_version();
		$transient_key      = $using_object_cache ? $cache_key : ( 'wc_report_' . $this->cache_key );
		$transient          = get_transient( $transient_key );

		if ( ! is_array( $transient ) ) {
			return false;
		}

		if ( $using_object_cache ) {

			if ( isset( $transient[ 'value' ], $transient[ 'version' ] ) && $transient[ 'version' ] === $transient_version ) {
				return $transient[ 'value' ];
			}

		} else {

			if ( isset( $transient[ $cache_key ], $transient[ $cache_key ][ 'value' ], $transient[ $cache_key ][ 'version' ] ) && $transient[ $cache_key ][ 'version' ] === $transient_version ) {
				return $transient[ $cache_key ][ 'value' ];
			}
		}

		return false;
	}

	/**
	 * Wrapper around DataStore::set_cached_data().
	 *
	 * @param string $cache_key Cache key.
	 * @param mixed  $value     New value.
	 * @return bool
	 */
	protected function set_cached_data( $cache_key, $value ) {

		if ( $this->should_use_cache() ) {

			$using_object_cache = wp_using_ext_object_cache();
			$transient_key      = $using_object_cache ? $cache_key : ( 'wc_report_' . $this->cache_key );
			$transient_version  = WC_Cache_Helper::get_transient_version( 'woocommerce_' . $this->cache_key . '_reports' ) . '_' . ReportsCache::get_version();

			if ( $using_object_cache ) {

				$transient = array(
					'version' => $transient_version,
					'value'   => $value,
				);

			} else {

				$transient = get_transient( $transient_key );

				// Cache up to 100 items.
				$count = -100;

				if ( ! is_array( $transient ) ) {
					$transient = array();
				}

				$transient_keys = array_keys( $transient );

				// Take the opportunity to clean up stale data.
				foreach ( $transient as $cached_data_key => $cached_data ) {

					if ( ! isset( $cached_data[ 'version' ] ) || $cached_data[ 'version' ] !== $transient_version ) {
						unset( $transient[ $cached_data_key ] );
					}

					if ( $count > -1 ) {
						unset( $transient[ $transient_keys[ $count ] ] );
					}

					$count++;
				}

				$transient[ $cache_key ] = array(
					'version' => $transient_version,
					'value'   => $value,
				);
			}

			$result = set_transient( $transient_key, $transient, WEEK_IN_SECONDS );

			return $result;
		}

		return true;
	}
}
