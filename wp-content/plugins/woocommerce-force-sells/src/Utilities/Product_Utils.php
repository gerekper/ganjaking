<?php
/**
 * Product utilities.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_Force_Sells\Utilities;

defined( 'ABSPATH' ) || exit;

use WC_Product;

/**
 * Class Product_Utils.
 */
class Product_Utils {

	/**
	 * Gets the product instance.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $the_product Post object or post ID of the product.
	 * @return WC_Product|false The product object. False on failure.
	 */
	public static function get_product( $the_product ) {
		return ( $the_product instanceof WC_Product ? $the_product : wc_get_product( $the_product ) );
	}

	/**
	 * Gets the forced sells of a product.
	 *
	 * @since 1.3.0
	 * @since 1.4.0 Renamed parameter `$return` to `$return_type`.
	 *
	 * @param int    $product_id  Product ID.
	 * @param string $type        Optional. Filter the forced sells by type. Default empty. Accepts: normal, synced.
	 * @param string $return_type Optional. What to return. Default: ids. Accepts: ids, objects.
	 * @return array
	 */
	public static function get_force_sells( $product_id, $type = '', $return_type = 'ids' ) {
		$allowed_types = array( 'normal', 'synced' );

		if ( $type && ! in_array( $type, $allowed_types, true ) ) {
			return array();
		}

		$force_sell_ids = array();

		foreach ( $allowed_types as $allowed_type ) {
			if ( $type && $type !== $allowed_type ) {
				continue;
			}

			$meta_key = ( 'normal' === $allowed_type ? '_force_sell_ids' : "_force_sell_{$allowed_type}_ids" );

			$ids = get_post_meta( $product_id, $meta_key, true );

			if ( is_array( $ids ) && ! empty( $ids ) ) {
				$force_sell_ids = array_merge( $force_sell_ids, $ids );
			}
		}

		$force_sell_ids = array_unique( $force_sell_ids );

		return array_values( 'ids' === $return_type ? $force_sell_ids : array_filter( array_map( 'wc_get_product', $force_sell_ids ) ) );
	}

	/**
	 * Gets the valid forced sells of a product.
	 *
	 * @since 1.3.0
	 * @since 1.4.0 Renamed parameter `$return` to `$return_type`.
	 *
	 * @see Product_Utils::get_force_sells()
	 * @see Product_Utils::force_sell_is_valid()
	 *
	 * @param int    $product_id  Product ID.
	 * @param string $type        Optional. Filter the forced sells by type. Default empty. Accepts: normal, synced.
	 * @param string $return_type Optional. What to return. Default: ids. Accepts: ids, objects.
	 * @return array
	 */
	public static function get_valid_force_sells( $product_id, $type = '', $return_type = 'ids' ) {
		$force_sells = self::get_force_sells( $product_id, $type, $return_type );

		return array_values( array_filter( $force_sells, array( __CLASS__, 'force_sell_is_valid' ) ) );
	}

	/**
	 * Checks if the provided product is a valid force sell.
	 *
	 * @since 1.3.0
	 *
	 * @param WC_Product|int $the_product Product object or ID.
	 * @return bool
	 */
	public static function force_sell_is_valid( $the_product ) {
		$product = self::get_product( $the_product );

		return ( $product && $product->exists() && 'trash' !== $product->get_status() );
	}
}
