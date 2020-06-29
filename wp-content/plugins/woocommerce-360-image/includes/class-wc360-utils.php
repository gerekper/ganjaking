<?php
/**
 * WooCommerce 360° Image Meta Boxes / Data
 *
 * @package   WooCommerce 360° Image
 * @author    bor0 <boro.sitnikovski@automattic.com>
 * @license   GPL-2.0+
 * @link      http://woocommerce.com/
 * @copyright 2017 WooCommerce
 * @since     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC360 Utils Class
 *
 * @package  WooCommerce 360° Image
 * @author   bor0 <boro.sitnikovski@automattic.com>
 * @since    1.1.0
 */

if ( ! class_exists( 'WC_360_Image_Utils' ) ) {

  class WC_360_Image_Utils {

	/**
	 * Retrieve image attachment IDs depending on WooCommerce version (3.0, 2.6, 2.5)
	 *
	 * @package WooCommerce 360° Image
	 * @author  Captain Theme <info@captaintheme.com>
	 * @since   1.0.0
	 */
	public static function get_gallery_ids( $product ) {
		if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
			// WC 3.0 support.
			return $product->get_gallery_image_ids();
		} else {
			// BWC for WC 2.6 and WC 2.5.
			return $product->get_gallery_attachment_ids();
		}
	}
  }

}
