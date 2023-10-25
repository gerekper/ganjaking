<?php
/**
 * Integration: WC Additional Variation Images
 *
 * @package Instagram\Integrations
 * @since   4.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Integration_Additional_Variation_Images
 */
class WC_Instagram_Integration_Additional_Variation_Images implements WC_Instagram_Plugin_Integration {

	/**
	 * Init.
	 *
	 * @since 4.5.0
	 */
	public static function init() {
		add_action( 'wc_instagram_product_additional_image_ids', array( __CLASS__, 'add_additional_variation_images' ), 10, 2 );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-additional-variation-images/woocommerce-additional-variation-images.php';
	}

	/**
	 * Adds the additional variation images.
	 *
	 * @since 4.5.0
	 *
	 * @param array      $image_ids The image IDs.
	 * @param WC_Product $product   Product object.
	 * @return array
	 */
	public static function add_additional_variation_images( $image_ids, $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$image_ids = array_merge( $image_ids, self::get_additional_variation_images( $product ) );
		}

		return $image_ids;
	}

	/**
	 * Gets the additional variation image IDs for the specified product.
	 *
	 * @since 4.5.0
	 *
	 * @param WC_Product $product Product object.
	 * @return array
	 */
	protected static function get_additional_variation_images( $product ) {
		$images = $product->get_meta( '_wc_additional_variation_images' );

		if ( ! $images ) {
			return array();
		}

		return array_filter( explode( ',', $images ) );
	}
}
