<?php
/**
 * WooCommerce 360° Image Meta Boxes / Data
 *
 * @package WooCommerce 360° Image
 * @since   1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC360 Utils Class
 */
class WC_360_Image_Utils {

	/**
	 * Retrieve image attachment IDs depending on WooCommerce version (3.0, 2.6, 2.5).
	 *
	 * @since 1.0.0
	 * @since 1.2.1 Always return an array.
	 *
	 * @param WC_Product $product Product object.
	 * @return array
	 */
	public static function get_gallery_ids( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return array();
		}

		if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
			// WC 3.0 support.
			return $product->get_gallery_image_ids();
		} else {
			// BWC for WC 2.6 and WC 2.5.
			return $product->get_gallery_attachment_ids();
		}
	}
}
