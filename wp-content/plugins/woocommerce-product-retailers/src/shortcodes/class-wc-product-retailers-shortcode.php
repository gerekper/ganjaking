<?php
/**
 * WooCommerce Product Retailers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * Product Retailers Shortcode
 *
 * Renders a list of product retailers
 *
 * @since 1.4.0
 */
class WC_Product_Retailers_Shortcode {


	/**
	 * Gets the shortcode content.
	 *
	 * @since 1.4
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public static function get( $atts ) {

		return \WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}


	/**
	 * Renders a set of retailers for a given product.
	 *
	 * All shortcode parameters are optional:
	 *
	 * + product_sku - use the product identified by SKU, otherwise default to the current product, if available
	 * + product_id - use the product with this ID, otherwise default to the current product, if available
	 *
	 * Example:
	 *
	 * [woocommerce_product_retailers product_id="123" product_sku="ABC123"]
	 *
	 * @since 1.4.0
	 *
	 * @param array<string, mixed>|scalar $attributes associative array of shortcode parameters
	 * @return void HTML
	 */
	public static function output( $attributes) {
		global $product, $wpdb;

		$attributes = shortcode_atts( [
			'product_id'  => '',
			'product_sku' => '',
		], (array) $attributes);

		// product by sku?
		if ( $attributes['product_sku'] ) {
			$attributes['product_id'] = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1", $attributes['product_sku'] ) );
		}


		// product by id?
		if ( $attributes['product_id'] ) {
			$product = wc_get_product( $attributes['product_id'] );
		}

		// if we don't have a product by now either none was specified, or the current page didn't have a default product (global $product above)
		if ( ! $product instanceof \WC_Product || \WC_Product_Retailers_Product::product_retailers_hidden_if_in_stock( $product ) ) {
			return;
		}

		// bail if product shouldn't be accessible
		if ( ! static::is_product_accessible( $product ) ) {
			return;
		}

		// render any product retailers
		woocommerce_single_product_product_retailers( $product );
	}


	/**
	 * Determines if a product can be accessed for outputting the shortcode data.
	 *
	 * @since 1.17.1
	 *
	 * @param WC_Product $product
	 * @return bool
	 */
	private static function is_product_accessible( \WC_Product $product ) : bool {

		// bail for products accessible by admins or editable by the user
		if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'edit_product', $product->get_id() ) ) {

			$is_accessible = true;

		// product is not meant to be visible or is unpublished
		} elseif ( ! $product->is_visible() || get_post_status( $product->get_id() ) !== 'publish' ) {

			$is_accessible = false;

		} else {

			$is_accessible = true;
			$product_post  = get_post( $product->get_id() );

			// product is password-protected
			if ( $product_post && ! empty( $product_post->post_password ) && post_password_required( $product_post->ID ) ) {
				$is_accessible = false;
			}
		}

		/**
		 * Filters whether a product can be accessed for outputting the shortcode data.
		 *
		 * @since 1.17.1
		 *
		 * @param bool $is_accessible
		 * @param WC_Product $product
		 */
		return (bool) apply_filters( 'wc_product_retailers_is_product_accessible', $is_accessible, $product );
	}


}
