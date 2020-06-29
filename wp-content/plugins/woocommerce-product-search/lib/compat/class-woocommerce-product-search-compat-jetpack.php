<?php
/**
 * class-woocommerce-product-search-compat-jetpack.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.10.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jetpack compatibility.
 */
class WooCommerce_Product_Search_Compat_Jetpack {


	/**
	 * Add action when handling a filter response.
	 */
	public static function init() {
		add_action( 'woocommerce_product_search_signal_filter_response', array( __CLASS__, 'woocommerce_product_search_signal_filter_response' ) );
		add_filter( 'jetpack_search_should_handle_query', array( __CLASS__, 'jetpack_search_should_handle_query' ), 10, 2 );
	}

	/**
	 * Selectively disable lazy loading on our filter response requests only,
	 * where lazy loading is redundant anyhow.
	 */
	public static function woocommerce_product_search_signal_filter_response() {
		add_filter( 'lazyload_is_enabled', array( __CLASS__, 'lazyload_is_enabled' ) );
	}

	/**
	 * Will disable lazy loading when the lazy-images Jetpack module is active.
	 * This is automatically triggered only when we handle our filter response.
	 *
	 * @param boolean $enabled
	 *
	 * @return boolean
	 */
	public static function lazyload_is_enabled( $enabled ) {
		if (
			class_exists( 'Jetpack' ) &&
			method_exists( 'Jetpack', 'is_module_active' ) &&
			Jetpack::is_module_active( 'lazy-images' )
		) {
			$enabled = false;
		}
		return $enabled;
	}

	/**
	 * Whether Jetpack's search should handle the query. Jetpack's search conflicts with the
	 * product search engine and reduces the set of products returned. To avoid this, we
	 * instruct Jetpack not to handle product-specific searches.
	 *
	 * @since 2.18.0
	 *
	 * @param boolean $should_handle
	 * @param WP_Query $query
	 *
	 * @return boolean whether Jetpack should handle the search or not
	 */
	public static function jetpack_search_should_handle_query( $should_handle, $query ) {
		$jetpack_should_handle = $should_handle;
		if ( $should_handle !== false ) {
			$post_type = $query->get( 'post_type' );
			if (
				(
					$query->is_search() || 
					$query->get( 'product_search', false ) 
				) &&
				(
					is_string( $post_type ) && $post_type === 'product' ||
					is_array( $post_type ) && count( $post_type ) === 1 && in_array( 'product', $post_type, true )
				)
			) {

				$should_handle = false;
			}
		}

		$should_handle = apply_filters( 'woocommerce_product_search_jetpack_search_should_handle_query', $should_handle, $jetpack_should_handle, $query );
		return $should_handle;
	}
}
WooCommerce_Product_Search_Compat_Jetpack::init();
