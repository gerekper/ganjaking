<?php
/**
 * WooCommerce 360° Image Meta Boxes / Data
 *
 * @package WC_360_Image
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC360 Utils Class.
 */
class WC_360_Image_Utils {

	/**
	 * Retrieve image attachment IDs.
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

		return $product->get_gallery_image_ids();
	}
}
