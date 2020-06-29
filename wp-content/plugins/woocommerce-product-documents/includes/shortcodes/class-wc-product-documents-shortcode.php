<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Documents Shortcode.
 *
 * Renders a list of product documents.
 *
 * @since 1.0
 */
class WC_Product_Documents_Shortcode {


	/**
	 * Gets the shortcode content.
	 *
	 * @since 1.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public static function get( $atts ) {

		return \WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}


	/**
	 * Renders a set of documents for a given product.
	 *
	 * All shortcode parameters are optional:
	 *
	 * + product_sku - use the product identified by SKU, otherwise default to the current product, if available
	 * + product_id - use the product with this ID, otherwise default to the current product, if available
	 * + title - the title to display over the product document list, defaults to the Product Documents title configured for the product
	 *
	 * Example:
	 *
	 * [woocommerce_product_documents product_id="123" product_sku="ABC123" title="Technical Data"]
	 *
	 * @param array $atts associative array of shortcode parameters
	 */
	public static function output( $atts ) {

		global $product, $wpdb;

		$atts = shortcode_atts( array(
			'product_id'  => '',
			'product_sku' => '',
			'title'       => '',
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
		if ( ! $product ) {
			return;
		}

		// default title?
		if ( ! $atts['title'] ) {
			$atts['title'] = wc_product_documents()->get_documents_title_text( $product->get_id() );
		}

		// Render any product documents
		woocommerce_product_documents_template( $product, $atts['title'] );
	}


}
