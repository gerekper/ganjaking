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
 * @copyright Copyright (c) 2013-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

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
	 * @param array $atts associative array of shortcode parameters
	 */
	public static function output( $atts ) {
		global $product, $wpdb;

		$atts = shortcode_atts( array(
			'product_id'  => '',
			'product_sku' => '',
		), $atts );

		// product by sku?
		if ( $atts['product_sku'] ) {
			$atts['product_id'] = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1", $atts['product_sku'] ) );
		}


		// product by id?
		if ( $atts['product_id'] ) {
			$product = wc_get_product( $atts['product_id'] );
		}

		// if we don't have a product by now either none was specified, or the current page didn't have a default product (global $product above)
		if ( ! $product || \WC_Product_Retailers_Product::product_retailers_hidden_if_in_stock( $product ) ) {
			return;
		}

		// Render any product retailers
		woocommerce_single_product_product_retailers( $product );
	}


}
