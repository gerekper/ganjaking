<?php
/**
 * Functions
 *
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_resource' ) ) {
	/**
	 * Get a resource.
	 *
	 * @param YITH_WCBK_Resource|WP_Post|int $resource The resource.
	 *
	 * @return YITH_WCBK_Resource|false
	 */
	function yith_wcbk_get_resource( $resource ) {
		try {
			return new YITH_WCBK_Resource( $resource );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_resources' ) ) {
	/**
	 * Get resources
	 *
	 * @param array $args The arguments.
	 *
	 * @return int[]|YITH_WCBK_Resource[]|false|object
	 */
	function yith_wcbk_get_resources( array $args = array() ) {
		try {
			/**
			 * The Resource Data Store
			 *
			 * @var YITH_WCBK_Resource_Data_Store $data_store
			 */
			$data_store = WC_Data_Store::load( 'yith-booking-resource' );

			return $data_store->query( $args );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_resource_data' ) ) {
	/**
	 * Return a Resource Data object.
	 *
	 * @param array|YITH_WCBK_Resource_Data $args Arguments.
	 *
	 * @return YITH_WCBK_Resource_Data
	 */
	function yith_wcbk_resource_data( $args ): YITH_WCBK_Resource_Data {
		return $args instanceof YITH_WCBK_Resource_Data ? $args : new YITH_WCBK_Resource_Data( $args );
	}
}

if ( ! function_exists( 'yith_wcbk_clear_resource_related_caches' ) ) {
	/**
	 * Clear cache related to resources.
	 *
	 * @param int|int[] $resource_ids Resource Ids.
	 */
	function yith_wcbk_clear_resource_related_caches( $resource_ids ) {
		$resource_ids = (array) $resource_ids;
		$resource_ids = array_filter( array_map( 'absint', $resource_ids ) );

		if ( $resource_ids ) {
			$product_ids = YITH_WCBK_Resources_Products::get_product_ids_with_resources( $resource_ids, 'in' );
			foreach ( $product_ids as $product_id ) {
				yith_wcbk_regenerate_product_data( $product_id );
			}
		}
	}
}
